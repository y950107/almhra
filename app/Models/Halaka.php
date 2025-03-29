<?php

namespace App\Models;

use App\Services\QuranPageCalculator;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class halaka extends Model
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
}
