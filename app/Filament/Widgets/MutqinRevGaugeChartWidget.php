<?php

namespace App\Filament\Widgets;

use App\Models\AlMaherRecitation;
use App\Models\AlMaqraaRecitation;
use App\Models\AlMutqinRecitation;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class MutqinRevGaugeChartWidget extends Widget
{
    protected static string $view = 'filament.widgets.custom-chart-widget';

    protected static ?int $sort = 3;
    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return false;
    }

    protected function getViewData(): array
    {
        $mutqinStart = Carbon::parse(settings('mutqin_start_date', '2024-09-01'));
        $mutqinEnd = Carbon::parse(settings('mutqin_end_date', '2025-06-01'));
        $defaultMonthlyTarget = (int) settings("mutqin_rev_monthly_target", 40);

        // Load all recitations with student and session in one query
        $cumulativeRecitations = AlMutqinRecitation::with('recitationSession.student')
            ->whereHas('recitationSession', function ($query) use ($mutqinStart, $mutqinEnd) {
                $query->whereBetween('session_date', [$mutqinStart, $mutqinEnd])
                    ->where('present', 'present');
            })->get();

        // Group recitations by student
        $grouped = $cumulativeRecitations->groupBy(fn($item) => $item->recitationSession->student_id);

        $cumulativeTarget = $cumulativePages = 0;

        foreach ($grouped as $recitations) {
            $student = $recitations->first()->recitationSession->student;

            if (!$student) {
                continue;
            }

            $monthlyTarget = (int) ($student->monthly_target_pages ?? $defaultMonthlyTarget);

            // Get tracking range per student without querying again
            $trackingStart = max(Carbon::parse($student->start_date), $mutqinStart);
            $trackingEnd = $mutqinEnd;

            // Count months between
            $months = (int) $trackingStart->startOfMonth()->diffInMonths($trackingEnd->endOfMonth()) + 1;

            // Filter recitations per student by tracking range
            $filteredRecitations = $recitations->filter(function ($item) use ($trackingStart, $trackingEnd) {
                $sessionDate = Carbon::parse($item->recitationSession->session_date);
                return $sessionDate->between($trackingStart, $trackingEnd);
            });


            $studentCumulativePages = $filteredRecitations->sum('rev_pages');

            $studentCumulativeTarget = $months * $monthlyTarget;

            $cumulativePages += $studentCumulativePages;
            $cumulativeTarget += $studentCumulativeTarget;
        }

        $cumulativePercentage = $cumulativeTarget > 0
            ? (int) round(($cumulativePages / $cumulativeTarget) * 100)
            : 0;

        return [
            'title' => 'نسبة الإنجاز برنامج المتقن (المراجعة)',
            'value' => $cumulativePercentage,
            'chartId' => 'gauge-chart-' . $this->getId(),
        ];
    }

}
