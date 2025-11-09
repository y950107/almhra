<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Student extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'teacher_id',
        'candidate_id',
        'start_date',
        'current_level',
        'monthly_target_pages',
        'monthly_excepted_months_pages',

        'maqraa_memorization_duration', //مدة الحفظ قسم المقراة
        'mutqin_memorization_duration', //مدة الحفظ قسم المتقن
        'mahir_memorization_duration', //مدة الحفظ قسم التأسيس
    ];
    protected $appends = ['full_name'];


    protected $casts = [
        'monthly_excepted_months_pages' => 'array',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recitationSessions()
    {
        return $this->hasMany(RecitationSession::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', "id");
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', "id");
    }

    public function sessions()
    {
        return $this->hasMany(halaka::class);
    }
    // public function halakas()
    // {
    //     return $this->belongsToMany(Halaka::class, 'recitation_sessions', 'student_id', 'halaka_id')
    //     ->distinct();
    // }
    public function halakas()
    {
        return $this->belongsToMany(Halaka::class, 'halaka_student', 'student_id', 'halaka_id')
            ->withPivot('attend_at', 'moved_at');

    }

    public function currentHalakas()
    {
        return $this->belongsToMany(Halaka::class, 'halaka_student', 'student_id', 'halaka_id')
            ->whereNull('moved_at')
            ->withPivot('attend_at', 'moved_at');

    }

    public function getFullNameAttribute()
    {
        return $this->candidate ? $this->candidate->full_name : ' اسم';
    }


    public function getActualMonthlyTargetAttribute(bool $mem = true)
    {
        $program = $this->candidate->program_type; // e.g., 'maqraa', 'mutqin', 'mahir'
        if ($program === 'mutqin') {
            $program = $mem ? "mutqin_mem" : "mutqin_rev";
        }

        $exceptedMonthsPages = $this->monthly_excepted_months_pages;
        $sum_exceptedMonthsPages = 0;
        if ($exceptedMonthsPages) {
            $exceptedMonthsPages = is_array($exceptedMonthsPages) ? $exceptedMonthsPages : json_decode($exceptedMonthsPages, true);

            foreach ($exceptedMonthsPages as $exceptedMonthPage) {
                $sum_exceptedMonthsPages += $exceptedMonthPage['count'];
            }
        }
        $monthly_target = $this->monthly_target_pages ? $this->monthly_target_pages : settings("{$program}_monthly_target", 40);
        $monthly_target -= $sum_exceptedMonthsPages;
        return (int)$monthly_target;

    }

    public function getProgramSettings(bool $mem = true): array
    {
        $program = $this->candidate->program_type; // e.g., 'maqraa', 'mutqin', 'mahir'

        if ($program === 'mutqin') {
            $monthly_target = $mem ? "mutqin_mem" : "mutqin_rev";
            $pages = $mem ? "mem_pages" : "rev_pages";
        } else {
            $monthly_target = "{$program}_monthly_target";
            $pages = "pages";
        }
        $exceptedMonthsPages = $this->monthly_excepted_months_pages;
        $sum_exceptedMonthsPages = 0;
        if ($exceptedMonthsPages) {
            $exceptedMonthsPages = is_array($exceptedMonthsPages) ? $exceptedMonthsPages : json_decode($exceptedMonthsPages, true);

            foreach ($exceptedMonthsPages as $exceptedMonthPage) {
                $sum_exceptedMonthsPages += $exceptedMonthPage['count'];
            }
        }
        $monthly_target = $this->monthly_target_pages ? $this->monthly_target_pages : settings("{$program}_monthly_target", 40);
        $monthly_target -= $sum_exceptedMonthsPages;


        return [
            'start' => Carbon::parse(settings("{$program}_start_date", '2024-09-01')),
            'end' => Carbon::parse(settings("{$program}_end_date", '2025-06-01')),
            'monthly_target' => (int)$monthly_target,
            'pages' => $pages
        ];
    }

    private function getRecitationModelClass(string $program): string
    {
        return match ($program) {
            'maqraa' => \App\Models\AlMaqraaRecitation::class,
            'mutqin' => \App\Models\AlMutqinRecitation::class,
            'mahir' => \App\Models\AlMaherRecitation::class,
            default => throw new \InvalidArgumentException("Unknown program type: $program"),
        };
    }

    private function getMemorizationDurationValue(string $program): string
    {
        return match ($program) {
            'maqraa' => $this->maqraa_memorization_duration,
            'mutqin' => $this->mutqin_memorization_duration,
            'mahir' => $this->mahir_memorization_duration,
            default => throw new \InvalidArgumentException("Unknown program type: $program"),
        };
    }

    public function calculateProgress($start, $end, $pages_att, bool $mem = true)
    {
        //if student start after the program
        if (Carbon::parse($this->start_date)->greaterThan($start)) {
            $start = Carbon::parse($this->start_date);
        }
        $program = $this->candidate->program_type;// e.g., 'maqraa', 'mutqin', 'mahir'

        $monthlyTarget = $this->getActualMonthlyTargetAttribute($mem);


        $monthsBetween = round($start->diffInMonths($end), 2);
        $monthsBetween = $monthsBetween < 1 ? 1 : $monthsBetween;

        // Dynamically resolve model class
        $modelClass = $this->getRecitationModelClass($program);

        $cumulativeRecitations = $modelClass::forStudentWithin([$start->toDateString(), $end->toDateString()], $this->id);

        $cumulativePages = $cumulativeRecitations->sum($pages_att);

        $cumulativeTarget = $monthsBetween * $monthlyTarget;

        //الحصول على المستهدف من مدة تسجيل الطالب فقط  الى تاريخ نهاية التقرير
        $memorization_duration = intval($this->getMemorizationDurationValue($program)) ? intval($this->getMemorizationDurationValue($program)) : 12;
        $requested_memorization_duration = $memorization_duration + ($this->start_date ? Carbon::parse($this->start_date)->month : 0);//المدة الالزامية

        $real_within_memorization_duration = round(Carbon::parse($this->start_date)->diffInMonths($end), 2); //المدة الحقيقية التي شملت حفظ الطالب مثلا قد حفظ لمدة شهرين من تاريخ بدأه فقط
        $real_within_memorization_cumulative_target = ($monthlyTarget * $real_within_memorization_duration);

        $cumulativeTarget = $real_within_memorization_cumulative_target;
        //نهاية الحصول على المستهدف من مدة تسجيل الطالب فقط الى تاريخ نهاية التقرير

        $percentage = $cumulativeTarget > 0
            ? round(($cumulativePages / $cumulativeTarget) * 100, 1)
            : 0;

        return [
            'cumulative_pages' => round($cumulativePages, 1),
            'cumulative_target' => round($cumulativeTarget, 1),
            'cumulative_percentage' => $percentage,
            'recitations_count' => $cumulativeRecitations->count()
        ];
    }

    public function getProgressPercentageAttribute()
    {
        $settings = $this->getProgramSettings();
        $dates = $this->calculateStartAndEndDates($settings);

        $start = $dates['start'];
        $end = $dates['end'];

        return $this->calculateProgress($start, $end, $settings['pages'])['cumulative_percentage'];
    }

    public function getProgramTypeAttribute()
    {

        return Candidate::getProgramTypes()[$this->candidate->program_type];
    }

    protected function getProgramEndDateAttribute()
    {
        try {
            $durationMonths = $this->getMemorizationDurationValue($this->candidate->program_type);

            if ($durationMonths) {
                return Carbon::parse($this->start_date)->addMonths((int)$durationMonths);
            }
        } catch (\Exception $e) {
            return null;
        }
        return  null;
    }

    public function getOnlineSessionsPercentageAttribute()
    {
        return $this->calculateRecitationPercentage('remote');
    }

    public function getPresentSessionsPercentageAttribute()
    {
        return $this->calculateRecitationPercentage('in_person');
    }

    protected function calculateRecitationPercentage(string $type): int
    {
        $model = $this->getRecitationModelClass($this->candidate->program_type);

        $data = $model::whereHas('recitationSession', function (Builder $query) {
            $query->where('student_id', $this->id)
                ->where('present', 'present');
        })
            ->with(['recitationSession' => function ($query) {
                $query->select('id', 'student_id', 'present', 'recitation_type');
            }])
            ->get()
            ->pluck('recitationSession.recitation_type');

        $total = $data->count();
        $present = $data->filter(fn($typeVal) => $typeVal === $type)->count();

        return $total > 0 ? (int)round(($present / $total) * 100) : 0;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function presentAttendances(): HasMany
    {
        return $this->attendances()->where('status', 'present');
    }

    public function absentAttendances(): HasMany
    {
        return $this->attendances()->whereNot('status', 'present');
    }

    public function isPresent($date = null)
    {
        // Use today's date if no date provided
        $date = $date ?: now()->format('Y-m-d');

        // Check if there's an attendance record for the date with status 'present'
        return $this->attendances()
            ->whereDate('date', $date)
            ->where('status', 'present')
            ->exists();
    }

    public function calculateStartAndEndDates($settings): array
    {

        $start = max(Carbon::parse($this->start_date), $settings['start']);

        $end = $this->program_end_date ?: $settings['end'];

        return [
            'start' => $start,
            'end' => $end,
        ];
    }




}
