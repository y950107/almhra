<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecitationSession extends Model 
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'halaka_id',         // معرف الحلقة التي ينتمي إليها هذا السجل
        'student_id',        // معرف الطالب
        'session_date',      // تاريخ ووقت الجلسة
        // أهداف الجلسة
        'start_surah',       // سورة البداية
        'start_ayah',        // رقم الآية للبداية
        'start_page',        // رقم صفحة البداية
        'end_surah',         // سورة النهاية
        'end_ayah',          // رقم الآية للنهاية
        'end_page',          // رقم صفحة النهاية
        // النتائج الفعلية (تدخلها المعلم بعد انتهاء الجلسة)
        'actual_end_surah',
        'actual_end_ayah',
        'actual_end_page',
        // تقييمات المعلم
        'tajweed_score',     // درجة التجويد
        'fluency_score',     // درجة الطلاقة
        'memory_score',      // درجة الحفظ
        'evaluation_notes',  // ملاحظات المعلم
        'notes',   
        'target_percentage',
        'Progress_percentage',
        'present_status',               
    ];

    // علاقة الجلسة بالحلبة
    public function halaka()
    {
        return $this->belongsTo(Halaka::class);
    }

    // علاقة الجلسة بالطالب
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

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

    
    public function getAchievementPercentageAttribute()
    {
        $target = $this->target_pages;
        if ($target > 0) {
            return round(($this->actual_pages / $target) * 100, 2);
        }
        return 0;
    }

    
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
}
