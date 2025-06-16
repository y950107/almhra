<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Halaka;
use Filament\Widgets\Widget;

class TeacherHalakaWidget extends Widget
{
    protected static string $view = 'filament.widgets.custom-chart-widget';

    protected static ?int $sort = 3;
    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';
    protected function getViewData(): array
    {

        $halaka = Halaka::where('teacher_id',auth()->user()->teacher->id)->first();

        $cumulativePercentage = $halaka->getProgressPercentageAttribute();

        return [
            'title' => 'نسبة انجاز الحلقة',
            'value' => $cumulativePercentage,
            'chartId' => 'gauge-chart-' . $this->getId(),
        ];
    }

}
