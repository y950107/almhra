<?php

namespace App\Models;

use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Halaka extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'teacher_id',
        'start_date',
        'max_students',
    ];

    // علاقة الحلقة بالمعلم
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    // علاقة الحلقة بجلسات التسميع الفردية
    public function recitationSessions()
    {
        return $this->hasMany(RecitationSession::class);
    }


    public function students()
    {
        return $this->hasManyThrough(Student::class, RecitationSession::class, 'halaka_id', 'id', 'id', 'student_id');
    }


    public function getStudentsCountAttribute()
    {
        $getcount = $this->students()->count();
        if ($getcount > 0) {
            return $getcount .'/'. app(GeneralSettings::class)->students_per_group .' '.'طالب';
        }
        return  $getcount .'/'. app(GeneralSettings::class)->students_per_group .' '.'طالب';
    }

    public function getStatusAttribute()
    {
        $countStudents = $this->students_count;
        $maxStudents = app(GeneralSettings::class)->students_per_group;
        return $countStudents > $maxStudents ? 'العدد مكتمل' : 'التسجيل متاح';
    }



    public function getProgressPercentageAttribute()
    {
        $program = $this->teacher->program_type; // e.g., 'maqraa', 'mutqin', 'mahir'
        $settings = $this->getProgramSettings($program);

        $start = $settings['start'];
        $end = $settings['end'];
        $monthlyTarget = $settings['monthly_target'];

        $monthsBetween = (int) $start->startOfMonth()->diffInMonths($end->endOfMonth()) + 1;

        // Dynamically resolve model class
        $modelClass = $this->getRecitationModelClass($program);

        $cumulativeRecitations = $modelClass::whereHas('recitationSession', function ($query) use ($start, $end) {
            $query->whereBetween('session_date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
                ->where('present', '=', 'present')
                ->where('halaka_id', $this->id);
        })->get();

        $cumulativePages = $cumulativeRecitations->sum('pages');
        $cumulativeTarget = $monthsBetween * $monthlyTarget;

        return $cumulativeTarget > 0
            ? (int) round(($cumulativePages / $cumulativeTarget) * 100)
            : 0;
    }


    private function getProgramSettings(string $program): array
    {
        return [
            'start' => Carbon::parse(settings("{$program}_start_date", '2024-09-01')),
            'end' => Carbon::parse(settings("{$program}_end_date", '2025-06-01')),
            'monthly_target' => (int) settings("{$program}_monthly_target", 40),
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

}
