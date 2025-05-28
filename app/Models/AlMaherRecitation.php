<?php

namespace App\Models;

use App\Services\Moshaf_madina_Service;
use Illuminate\Database\Eloquent\Model;

class AlMaherRecitation extends Model
{

    protected $table = "almaher_recitations";

    protected $fillable = [
        'recitation_session_id',
        'start_surah_id',
        'start_ayah_id',
        'end_surah_id',
        'end_ayah_id',
        'pages',
        'lesson_title',
        'mem_lines',
    ];


    public function recitationSession()
    {
        return $this->belongsTo(RecitationSession::class,'recitation_session_id','id');
    }

    public static function getLessonTitles(): array
    {
        return [
            'tohfa' => 'متن تحفة الأطفال',
            'jarzeya' => 'متن الجزرية',
            'none' => 'لا يوجد مقرر',
        ];
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

