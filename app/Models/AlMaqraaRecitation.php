<?php

namespace App\Models;

use App\Services\Moshaf_madina_Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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

    public static function getTotalActualPages()
    {
        return self::select(DB::raw('SUM(pages) as total_actual'))->first()->total_actual ?? 0;
    }

    public static function getTotalTargetPagesPerStudent()
    {



        return self::select(
            'student_id',
            DB::raw('SUM(target_pages) as total_target_pages')
        )
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');
    }

    public static function getTotalActualPagesPerStudent()
    {
        return self::join('recitation_sessions', 'almaqraa_recitations.recitation_session_id', '=', 'recitation_sessions.id')
            ->select(
                'recitation_sessions.student_id',
                DB::raw('SUM(almaqraa_recitations.pages) as total_actual_pages')
            )
            ->groupBy('recitation_sessions.student_id')
            ->get()
            ->keyBy('student_id');
    }

}

