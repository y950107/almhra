<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Student extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'evaluator_id',
        'candidate_id',
        'start_date',
        'current_level',
        'monthly_target_pages'
    ];
    protected $appends = ['full_name'];



    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class,'evaluator_id',"id");
    }
    public function candidate()
    {
        return $this->belongsTo(Candidate::class,'candidate_id',"id");
    }

    public function sessions()
    {
        return $this->hasMany(halaka::class);
    }
    public function getFullNameAttribute()
    {
        return $this->candidate ? $this->candidate->full_name : ' اسم';
    }
    public function recitationSessions()
    {
        return $this->hasMany(RecitationSession::class);
    }

    public function getProgressPercentageAttribute()
    {
        $program = $this->teacher->program_type; // e.g., 'maqraa', 'mutqin', 'mahir'
        $settings = $this->getProgramSettings($program);

        $start = max(Carbon::parse($this->start_date), $settings['start']);
        $end = $settings['end'];
        $monthlyTarget =  $this->monthly_target_pages ?? $settings['monthly_target'];

        $monthsBetween =  round( $start->startOfMonth()->diffInMonths($end->endOfMonth())) + 1;

        // Dynamically resolve model class
        $modelClass = $this->getRecitationModelClass($program);

        $cumulativeRecitations = $modelClass::whereHas('recitationSession', function ($query) use ($start, $end) {
            $query->whereBetween('session_date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
                ->where('present', '=', 'present')
                ->where('student_id', $this->id);
        })->get();

        $cumulativePages = $cumulativeRecitations->sum('pages');

        $cumulativeTarget = $monthsBetween * $monthlyTarget;

        return $cumulativeTarget > 0
            ? (int) round(($cumulativePages / $cumulativeTarget) * 100)
            : 0;
    }


    private function getProgramSettings(string $program): array
    {
        return [
            'start' => Carbon::parse(settings("{$program}_start_date", '2024-09-01')),
            'end' => Carbon::parse(settings("{$program}_end_date", '2025-06-01')),
            'monthly_target' => (int) settings("{$program}_monthly_target", 40),
        ];
    }
    private function getRecitationModelClass(string $program): string
    {
        return match ($program) {
            'maqraa' => \App\Models\AlMaqraaRecitation::class,
            'mutqin' => \App\Models\AlMutqinRecitation::class,
            'mahir'  => \App\Models\AlMaherRecitation::class,
            default => throw new \InvalidArgumentException("Unknown program type: $program"),
        };
    }

}
