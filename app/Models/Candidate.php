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
        'national_id',
        'phone',
        'email',
        'birthdate',
        'qualification',
        'quran_level',
        'has_ijaza',
        'ijaza_types',
        'desired_recitation',
        'self_evaluation',
        'user_id',
        'program_type',
        'teacher_id',
        'qualification_file',
        'audio_recitation',
        'status',
        'interview_date',
        'interview_type',
        'evaluated'//
    ];


    protected $casts = [
        'ijaza_types' => 'array',
        'birthdate' => 'date',
        'interview_date' => 'date',
        'has_ijaza' => 'boolean',
        'status' => CandidateStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'candidate_id', 'id');
    }


    public static function getProgramTypes(): array
    {
        return ['maqraa' => 'برنامج المقرأة', 'mutqin' => 'برنامج المتقن', 'mahir' => 'برنامج التأسيس'];
    }

    public static function getQuranLevels(): array
    {
        return ['beginner' => 'مبتدئ', 'intermediate' => 'متوسط', 'advanced' => 'متقدم'];
    }




}
