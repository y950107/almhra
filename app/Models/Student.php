<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'evaluator_id',
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

    public function evaluator()
    {
        return $this->belongsTo(User::class,'evaluator_id',"id");
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
