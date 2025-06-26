<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class MahirGaugeChartWidget extends Widget
{
    protected static string $view = 'filament.widgets.custom-chart-widget';

    protected static ?int $sort = 2;
    protected static bool $isLazy = false;



    protected function getViewData(): array
    {

        $students = Student::with('candidate')
            ->whereHas('candidate', fn ($q) => $q->where('program_type', 'mahir'))
            ->get();

        $percentages = $students->map(function ($student) {
            $settings = $student->getProgramSettings();
            $start = max(Carbon::parse($student->start_date), $settings['start']);
            $end = $settings['end'];
            $pages_att = $settings['pages'];

            return $student->calculateProgress($start, $end, $pages_att)['cumulative_percentage'];
        })->filter(fn ($v) => $v !== null)->values();

        $average = $percentages->count() > 0
            ? round($percentages->avg(), 2)
            : 0;

        return [
            'title' => 'نسبة الإنجاز برنامج التأسيس',
            'value' => $average,
            'chartId' => 'gauge-chart-' . $this->getId(),
        ];
    }

}
