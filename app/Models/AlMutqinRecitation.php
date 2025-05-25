<?php

namespace App\Models;

use App\Services\Moshaf_madina_Service;
use Illuminate\Database\Eloquent\Model;

class AlMutqinRecitation extends Model
{

    protected $table = "almutqin_recitations";

    protected $fillable = [
        'recitation_session_id',
        'mem_start_surah_id',
        'mem_start_ayah_id',
        'mem_end_surah_id',
        'mem_end_ayah_id',
        'mem_pages',
        'rev_start_surah_id',
        'rev_start_ayah_id',
        'rev_end_surah_id',
        'rev_end_ayah_id',
        'rev_pages',
        'lesson_title',
        'lesson_type',
    ];


    public function recitationSession()
    {
        return $this->belongsTo(RecitationSession::class);
    }

    public static function getLessonTypes(): array
    {
        return [
            'fiqh' => 'الفقه',
            'tafsir' => 'التفسير',
            'akhlek' => 'الاداب والاخلاق' ,
            'adhkar' => 'الاذكار' ,
            'tajweed' => 'التجويد'];
    }

    public function getMemSurahNameAttribute()
    {
        $quranService = app(Moshaf_madina_Service::class);
        $surahs = $quranService->getSurahs();
        return collect($surahs)->where('id', $this->mem_end_surah_id)->first()['name'] ?? null;
    }

    public function getRevSurahNameAttribute()
    {
        $quranService = app(Moshaf_madina_Service::class);
        $surahs = $quranService->getSurahs();
        return collect($surahs)->where('id', $this->rev_end_surah_id)->first()['name'] ?? null;
    }

}

