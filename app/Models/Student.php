<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
        'monthly_target_pages'
    ];
    protected $appends = ['full_name'];



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
        return $this->belongsTo(Teacher::class,'teacher_id',"id");
    }
    public function candidate()
    {
        return $this->belongsTo(Candidate::class,'candidate_id',"id");
    }

    public function sessions()
    {
        return $this->hasMany(halaka::class);
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

        return (int) ($student->monthly_target_pages ?? settings("{$program}_monthly_target", 40));

    }

    public function getProgramSettings(bool $mem = true): array
    {
        $program = $this->candidate->program_type; // e.g., 'maqraa', 'mutqin', 'mahir'

        if ($program === 'mutqin') {
            $monthly_target = $mem ? "mutqin_mem" : "mutqin_rev";
            $pages = $mem ? "mem_pages" : "rev_pages";
        }
        else {
            $monthly_target = "{$program}_monthly_target";
            $pages = "pages";
        }

        return [
            'start' => Carbon::parse(settings("{$program}_start_date", '2024-09-01')),
            'end' => Carbon::parse(settings("{$program}_end_date", '2025-06-01')),
            'monthly_target' => (int) ($student->monthly_target_pages ?? settings($monthly_target, 40)),
            'pages' => $pages
        ];
    }
    private function getRecitationModelClass(string $program): string
    {
        return match ($program) {
            'maqraa' => \App\Models\AlMaqraaRecitation::class,
            'mutqin' => \App\Models\AlMutqinRecitation::class,
            'mahir'  => \App\Models\AlMaherRecitation::class,
            default => throw new \InvalidArgumentException("Unknown program type: $program"),
        };
    }

    public function calculateProgress( $start , $end , $pages_att , bool $mem = true )
    {
        $program = $this->candidate->program_type;// e.g., 'maqraa', 'mutqin', 'mahir'

        $monthlyTarget =  $this->getActualMonthlyTargetAttribute($mem);

        $monthsBetween =  round( $start->diffInMonths($end),2) ;


        // Dynamically resolve model class
        $modelClass = $this->getRecitationModelClass($program);

        $cumulativeRecitations = $modelClass::forStudentWithin([$start->toDateString(), $end->toDateString()] , $this->id);

        $cumulativePages = $cumulativeRecitations->sum($pages_att);

        $cumulativeTarget = $monthsBetween * $monthlyTarget;

        $percentage = $cumulativeTarget > 0
            ?  round(($cumulativePages / $cumulativeTarget) * 100,1)
            : 0;

        return [
            'cumulative_pages' => round($cumulativePages,1),
            'cumulative_target' => round($cumulativeTarget,1),
            'cumulative_percentage' => $percentage,
            'recitations_count' => $cumulativeRecitations->count()
        ];
    }

    public function getProgressPercentageAttribute()
    {
        $settings = $this->getProgramSettings();

        $start = max(Carbon::parse($this->start_date), $settings['start']);
        $end = $settings['end'];
        return $this->calculateProgress($start,$end,$settings['pages'])['cumulative_percentage'];
    }

    public function getProgramTypeAttribute()
    {

        return Candidate::getProgramTypes()[$this->candidate->program_type];
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

        return $total > 0 ? (int) round(($present / $total) * 100) : 0;
    }




}
