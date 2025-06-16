<?php

namespace App\Models;

use App\Models\Traits\HandlesRecitations;
use Illuminate\Database\Eloquent\Model;

class AlMaherRecitation extends Model
{
    use HandlesRecitations;

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
        return self::getAyahText($this->end_surah_id,$this->end_ayah_id);
    }

    public function getSurahNameAttribute()
    {
        return self::getSurahName($this->end_surah_id);
    }

}

