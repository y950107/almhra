<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class MutqinRevGaugeChartWidget extends Widget
{
    protected static string $view = 'filament.widgets.custom-chart-widget';

    protected static ?int $sort = 3;
    protected static bool $isLazy = false;



    protected function getViewData(): array
    {
        $students = Student::with('candidate')
            ->whereHas('candidate', fn ($q) => $q->where('program_type', 'mutqin'))
            ->get();

        $percentages = $students->map(function ($student) {
            return $student->getProgressPercentageAttribute();
        })->filter(fn ($v) => $v !== null)->values();

        $average = $percentages->count() > 0
            ? round($percentages->avg(), 2)
            : 0;

        return [
            'title' => 'نسبة الإنجاز برنامج المتقن (المراجعة)',
            'value' => $average,
            'chartId' => 'gauge-chart-' . $this->getId(),
        ];
    }

}
