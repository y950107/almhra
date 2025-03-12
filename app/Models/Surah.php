<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Surah extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        
    ];




    // العلاقات
   
    // public function recitationSessions()
    // {
    //     return $this->hasMany(RecitationSession::class);
    // }
}
