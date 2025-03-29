<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Teacher extends Model{

        use Notifiable;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'qualifications',
        'expertise',
        'certifications',
        'user_id',   
    ];

    protected $casts = [
        'qualifications' => 'array',
        'expertise' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function hasAccount(): bool
    {
        return !empty($this->user_id);
    }
}
