<?php

namespace App\Filament\Student\Widgets;

use App\Models\Student;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatStudent extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;
    protected ?string $heading = 'احصائيات خاصة  بحصص التسميع ';

    protected function getStats(): array
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        $settings = $student->getProgramSettings();
        $start = max(Carbon::parse($student->start_date), $settings['start']);
        $stats = $student->calculateProgress($start,$settings['end'],$settings['pages']);

        return [
            Stat::make('إجمالي الأوجه المحققة', $stats['cumulative_pages'])
                ->description("نسبة الإنجاز {$stats['cumulative_percentage']}%")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('إجمالي الأوجه المستهدفة', $stats['cumulative_target'])
                ->description('منذ بداية البرنامج')
                ->color('info'),

            Stat::make('عدد الحصص', $stats['recitations_count']),
        ];
    }

}
