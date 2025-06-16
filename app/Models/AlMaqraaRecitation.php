<?php

namespace App\Models;

use App\Models\Traits\HandlesRecitations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlMaqraaRecitation extends Model
{
    use HandlesRecitations;

    protected $table = "almaqraa_recitations";

    protected $fillable = [
        'recitation_session_id',
        'start_surah_id',
        'start_ayah_id',
        'end_surah_id',
        'end_ayah_id',
        'pages',
    ];

    public $timestamps = true;

    protected $with = ['recitationSession'];


    public function recitationSession() : BelongsTo
    {
        return $this->belongsTo(RecitationSession::class,'recitation_session_id','id');
    }

    public function getAyahTextAttribute()
    {
        return self::getAyahText($this->end_surah_id,$this->end_ayah_id);
    }

    public function getSurahNameAttribute()
    {
        return self::getSurahName($this->end_surah_id);
    }



}

