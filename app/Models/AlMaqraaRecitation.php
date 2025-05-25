<?php

namespace App\Models;

use App\Services\Moshaf_madina_Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlMaqraaRecitation extends Model
{

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
        $quranService = app(Moshaf_madina_Service::class);
        $ayahs = $quranService->getAyahs($this->end_surah_id);
        return collect($ayahs)->where('number', $this->end_ayah_id)->first()['text'] ?? null;
    }

    public function getSurahNameAttribute()
    {
        $quranService = app(Moshaf_madina_Service::class);
        $surahs = $quranService->getSurahs();
        return collect($surahs)->where('id', $this->end_surah_id)->first()['name'] ?? null;
    }

}

