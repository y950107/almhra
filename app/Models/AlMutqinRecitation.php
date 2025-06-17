<?php

namespace App\Models;

use App\Models\Traits\HandlesRecitations;
use Illuminate\Database\Eloquent\Model;

class AlMutqinRecitation extends Model
{

    use HandlesRecitations;

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

    public  function getTranslatedLessonTypeAttribute()
    {
        return $this->lesson_type ? self::getLessonTypes()[$this->lesson_type] : "";
    }


    public function getMemAyahTextAttribute()
    {
        return self::getAyahText($this->mem_end_surah_id,$this->mem_end_ayah_id);
    }

    public function getMemSurahNameAttribute()
    {
        return self::getSurahName($this->mem_end_surah_id);
    }

    public function getRevAyahTextAttribute()
    {
        return self::getAyahText($this->rev_end_surah_id,$this->rev_end_ayah_id);
    }

    public function getRevSurahNameAttribute()
    {
        return self::getSurahName($this->rev_end_surah_id);
    }


}

