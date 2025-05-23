<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Student extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'teacher_id',
        'candidate_id',
        'start_date',
        'current_level'
    ];
    protected $appends = ['full_name'];



    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function sessions()
    {
        return $this->hasMany(halaka::class);
    }
    public function getFullNameAttribute()
    {
        return $this->candidate ? $this->candidate->full_name : ' اسم';
    }
    public function recitationSessions()
    {
        return $this->hasMany(RecitationSession::class);
    }
}
