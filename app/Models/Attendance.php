<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT_WITH_EXCUSE = 'absent_with_excuse';
    const STATUS_ABSENT_WITHOUT_EXCUSE = 'absent_without_excuse';
    protected $fillable = [
        'student_id',
        'date',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // Scope for filtering
    public function scopeToday($query)
    {
        return $query->where('date', today());
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
     public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }
    public function scopeAbsentWithExcuse($query)
    {
        return $query->where('status', self::STATUS_ABSENT_WITH_EXCUSE);
    }

    public function scopeAbsentWithoutExcuse($query)
    {
        return $query->where('status', self::STATUS_ABSENT_WITHOUT_EXCUSE);
    }
}