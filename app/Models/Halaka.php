<?php

namespace App\Models;

use App\Services\QuranPageCalculator;
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
        return $this->belongsTo(Teacher::class);
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
        if($getcount>0){
            return $getcount;
        }
        return 'لا طلاب ';
    } 

        public function getStatusAttribute()
    {
        return $this->students_count >= $this->max_students ? ' العدد مكتمل' : 'السجيل متاح';
    } 
}
