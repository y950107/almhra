<?php

namespace App\Filament\Student\Widgets;

use App\Models\Student;
use Filament\Widgets\Widget;

class StudentProgressPercentageWidget extends Widget
{
    protected static string $view = 'filament.widgets.custom-chart-widget';

    protected static ?int $sort = 2;
    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';
    protected function getViewData(): array
    {

        $student = Student::where('user_id',auth()->user()->id)->first();

        $cumulativePercentage = $student?->getProgressPercentageAttribute();

        return [
            'title' => 'نسبة انجاز الطالب',
            'value' => $cumulativePercentage ?? 0,
            'chartId' => 'gauge-chart-' . $this->getId(),
        ];
    }

}
