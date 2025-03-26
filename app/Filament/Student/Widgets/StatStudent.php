<?php

namespace App\Filament\Student\Widgets;

use App\Models\Halaka;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Candidate;
use App\Models\RecitationSession;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatStudent extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;
    protected ?string $heading = 'احصائيات خاصة  بحصص التسميع ';
    protected function getDescription(): ?string
{
    return 'An overview of some analytics.';
}
    protected function getStats(): array
    {
        $recitationStats = RecitationSession::whereHas('student', function ($query) {
            $query->where('user_id', auth()->id());
        });

        $totalActualPages = $recitationStats->sum('actual_end_page');
        $totalTargetPages = $recitationStats->sum('target_pages');
        $totalActuel_lines = $recitationStats->sum('actuel_lines');
        $averageTargetPercentage = $totalTargetPages != 0 ? ($totalActualPages / $totalTargetPages) * 100 : 0;
        $totalRecitation = $recitationStats->count('id');



        return [
            Stat::make('عدد الاوجه المحققة', $totalActualPages)
                ->description('نسبة % ')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),


            Stat::make('مجموع الاوجه المستهدفة', $totalTargetPages)
                ->description('إجمالي الأوجه المستهدفة')
                ->descriptionIcon('heroicon-m-clipboard-document')
                ->color('info')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                ]),
                
            Stat::make('نسبة الانجاز', $averageTargetPercentage . '%')
            ->chart([7, 50, 70, 90, 50, 30, 20])
            ->color('success'),



            Stat::make('عدد حصص التسميع  ', $totalRecitation),
            // Stat::make('عدد الطلاب المحولين إلى الحلقات المفتوحة', Student::whereNotNull('halaka_id')->count()),
        ];
    }
}
