<?php

namespace App\Models;

use App\Enums\CandidateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Candidate extends Model
{
    use Notifiable;
   
    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'birthdate',
        'qualification',
        'quran_level',
        'has_ijaza',
        'ijaza_types',
        'desired_recitations',//  القراءات المراد  قراءتها 
        'self_evaluation',
        'teacher_id',  // المعلم المراد الدراسة عنده 
        'qualification_file', // التقييم الشخصي 
        'audio_recitation', // مقطع صوتي 
        'status', //الحالة
        'interview_date',// تاريخ المقابلة
        'interview_type'// نوع المقابلة قادر تكون  adictance  من  بعد نديروها
    ];

    protected $casts = [
        'ijaza_types' => 'array',
        'desired_recitations' => 'array',
        'birthdate' => 'date',
        'interview_date' => 'datetime',
        'status' => CandidateStatus::class,
    ];


    public function teacher()
{
    return $this->belongsTo(Teacher::class, 'teacher_id');
}
}
