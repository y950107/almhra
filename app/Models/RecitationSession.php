<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use App\Services\Moshaf_madina_Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecitationSession extends Model
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'halaka_id',         
        'student_id',  
        'session_date',      
        'start_ayah_id',
        'end_surah_id',
        'end_ayah_id',
        'start_surah_id',
        'start_page',        
        'end_page',
        'target_lines',
        'target_pages',
        'actual_end_surah_id',
        'actual_end_ayah_id',
        'actual_end_surah',
        'actual_end_ayah',
        'actual_end_page',
        'actuel_lines',
        'actual_pages',
        'tajweed_score',    
        'fluency_score',     
        'memory_score',      
        'evaluation_notes',  
        'notes',
        'target_percentage',
        'Progress_percentage',
        'present_status',
    ];

    
    public function halaka()
    {
        return $this->belongsTo(Halaka::class);
    }


    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    /********** */


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
        return self::select(
                'student_id',
                DB::raw('SUM(actual_pages) as total_actual_pages')
            )
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');
    }

    public static function getTotalTargetPages(){
        return self::select(DB::raw('SUM(target_pages) as total_target'))->first()->total_target ?? 0;
    }

    // هذي نعدلها  باه تجيب  مجموع التراكمي مستهدف و محقق
    public static function getTotalTarget(){
        return self::select(DB::raw('SUM(target_pages) as total_target'))->first()->total_target ?? 0;
    }


    public static function getTotalActualPages(){
        return self::select(DB::raw('SUM(actual_pages) as total_actual'))->first()->total_actual ?? 0;
    }

    public function getStartSurahNameAttribute()
    {
        $quranService = app(Moshaf_madina_Service::class);
        $surahs = $quranService->getSurahs();
        return collect($surahs)->where('id', $this->start_surah_id)->first()['name'] ?? null;
    }

    public function getStartAyahTextAttribute()
    {
        $quranService = app(Moshaf_madina_Service::class);
        $ayahs = $quranService->getAyahs($this->start_surah_id);
        return collect($ayahs)->where('number', $this->start_ayah_id)->first()['text'] ?? null;
    }

    public function getEndSurahNameAttribute()
    {
        $quranService = app(Moshaf_madina_Service::class);
        $surahs = $quranService->getSurahs();
        return collect($surahs)->where('id', $this->end_surah_id)->first()['name'] ?? null;
    }

    public function getEndAyahTextAttribute()
    {
        $quranService = app(Moshaf_madina_Service::class);
        $ayahs = $quranService->getAyahs($this->end_surah_id);
        return collect($ayahs)->where('number', $this->end_ayah_id)->first()['text'] ?? null;
    }

    public function getActualEndAyahTextAttribute()
    {
        $quranService = app(Moshaf_madina_Service::class);
        $ayahs = $quranService->getAyahs($this->actual_end_surah_id);
        return collect($ayahs)->where('number', $this->actual_end_ayah_id)->first()['text'] ?? null;
    }
}






    //************************************ */


    // public function startSurah()
    // {
    //     return $this->belongsTo(Surah::class, 'start_surah_id');
    // }

    // public function startAyah()
    // {
    //     return $this->belongsTo(Verse::class, 'start_ayah_id');
    // }

    // public function endSurah()
    // {
    //     return $this->belongsTo(Surah::class, 'end_surah_id');
    // }

    // public function endAyah()
    // {
    //     return $this->belongsTo(Verse::class, 'end_ayah_id');
    // }

    // public function actualEndSurah()
    // {
    //     return $this->belongsTo(Surah::class, 'actual_end_surah_id');
    // }

    // public function actualEndAyah()
    // {
    //     return $this->belongsTo(Verse::class, 'actual_end_ayah_id');
    // }

    // public function getTargetPagesAttribute()
    // {
    //     return abs(($this->end_page ?? $this->start_page) - $this->start_page);
    // }

    // public function getActualPagesAttribute()
    // {
    
    //     $lastSession = RecitationSession::where('student_id', $this->student_id)
    //         ->where('session_date', '<', $this->session_date)
    //         ->orderBy('session_date', 'desc')
    //         ->first();

       
    //     $actualPages = abs($this->actual_end_page - $this->start_page) + 1;

     
    //     if ($lastSession && $this->start_page === $lastSession->actual_end_page) {
    //         $actualPages = max(0, $actualPages - 1); 
    //     }

    //     return $actualPages;
    // }




    // public function getAchievementPercentageAttribute()
    // {
    //     return $this->target_pages > 0 ? round(($this->actual_pages / $this->target_pages) * 100, 2) : 0;
    // }

    // حساب عدد الصفحات المستهدفة (target_pages) كفارق مطلق بين صفحة النهاية وصفحة البداية
    // public function getTargetPagesAttribute()
    // {
    //     return abs($this->end_page - $this->start_page);
    // }

    // حساب عدد الصفحات الفعلية (actual_pages) بناءً على صفحة النهاية الفعلية المدخلة
    // public function getActualPagesAttribute()
    // {
    //     if (!is_null($this->actual_end_page)) {
    //         return abs($this->actual_end_page - $this->start_page);
    //     }
    //     return 0;
    // }

    /*     public function getAchievementPercentageAttribute()
{
    if ($this->end_page && $this->start_page) {
        $plannedPages = $this->end_page - $this->start_page + 1;
        $actualPages = $this->actual_end_page ? $this->actual_end_page - $this->start_page + 1 : 0;
        return $plannedPages > 0 ? round(($actualPages / $plannedPages) * 100, 2) : 0;
    }
    return 0;
}

public function getWeightedAchievementAttribute()
{
    $score = ($this->tajweed_score + $this->fluency_score + $this->memory_score) / 3;
    return round($score, 2);
} */


    // public function getAchievementPercentageAttribute()
    // {
    //     $target = $this->target_pages;
    //     if ($target > 0) {
    //         return round(($this->actual_pages / $target) * 100, 2);
    //     }
    //     return 0;
    // }


    /* public function getWeightedAchievementAttribute()
{
    $memoryWeight  = (float) Settings::getValue('evaluation.memory_weight', 0.5);
    $tajweedWeight = (float) Settings::getValue('evaluation.tajweed_weight', 0.3);
    $fluencyWeight = (float) Settings::getValue('evaluation.fluency_weight', 0.2);

    $memoryScore  = $this->memory_score ?? 0;
    $tajweedScore = $this->tajweed_score ?? 0;
    $fluencyScore = $this->fluency_score ?? 0;

    return round(($memoryScore * $memoryWeight) + ($tajweedScore * $tajweedWeight) + ($fluencyScore * $fluencyWeight), 2);
} */

