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
            try {
                $count = $this->teacher->students()->count() ;
                // $halaka->students()->distinct('students.id')->count('students.id')
                $max = app(GeneralSettings::class)->students_per_group;
                return "$count/$max طالب";
            } catch (\Exception $e) {
                return '0/0 طالب (خطأ)';
            }
    }

    public function getStatusAttribute()
    {
        $countStudents = $this->students_count;
        $maxStudents = app(GeneralSettings::class)->students_per_group;
        return $countStudents > $maxStudents ? 'العدد مكتمل' : 'التسجيل متاح';
    }



    public function getProgressPercentageAttribute()
    {
        $students = $this->students;
        $percentages = $students->map(function ($student) {
            $settings = $student->getProgramSettings();
            $start = max(Carbon::parse($student->start_date), $settings['start']);
            $end = $settings['end'];
            $pages_att = $settings['pages'];

            return $student->calculateProgress($start, $end, $pages_att)['cumulative_percentage'];
        })->filter(fn ($v) => $v !== null)->values();


        return $percentages->count() > 0
            ? round($percentages->avg(), 1)
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
