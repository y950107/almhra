<?php

namespace App\Models;

use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
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
        return $this->belongsToMany(Student::class, 'halaka_student', 'halaka_id', 'student_id')
            ->withPivot('attend_at','moved_at');
    }

    public function currentStudents()
    {
        return $this->belongsToMany(Student::class, 'halaka_student', 'halaka_id', 'student_id')
            ->wherePivot('moved_at', null)
            ->withPivot('moved_at','attend_at');
    }

    public function getStudentsCountAttribute()
    {
            try {
                // $hasSessions = DB::table('recitation_sessions')
                // ->where('halaka_id', $this->id)
                // ->exists();

                // if ($hasSessions) {
                //     // Case 1: Count students with sessions in this halaka
                //     $count = DB::table('recitation_sessions')
                //         ->where('halaka_id', $this->id)
                //         ->distinct('student_id')
                //         ->count('student_id');
                // } else {
                //     // Case 2: Count teacher's students without any sessions
                //     $count = $this->teacher->students()
                //         ->whereDoesntHave('recitationSessions')
                //         ->count();
                // }
                // $halaka->students()->distinct('students.id')->count('students.id')
                $count = $this->currentStudents()->count();
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
            return $student->getProgressPercentageAttribute();
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
