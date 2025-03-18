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
        'desired_recitations',
        'self_evaluation',
        'teacher_id',  
        'qualification_file',
        'audio_recitation',
        'status', 
        'interview_date',
        'interview_type'// 
    ];

    protected $casts = [
        'qualification' => 'array',
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

public static function getQuranLevels(): array
{
    return ['beginner' => 'مبتدئ', 'intermediate' => 'متوسط', 'advanced' => 'متقدم'];
}

public static function getIjazaTypes(): array
{
    return [
        'hafs' => 'إجازة برواية حفص',
        'warsh' => 'إجازة برواية ورش',
        'qalun' => 'إجازة برواية قالون',
        'duri' => 'إجازة برواية الدوري',
        'susi' => 'إجازة برواية السوسي',
        'shuba' => 'إجازة برواية شعبة',
        'khalaf' => 'إجازة برواية خلف عن حمزة',
        'ibn_kathir' => 'إجازة برواية ابن كثير'
    ];
}
}
