<?php

namespace App\Filament\Widgets;

use App\Models\Halaka;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Candidate;
use App\Models\RecitationSession;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;


class StatDashboardNew extends BaseWidget
{
    protected static ?int $sort =1;
    protected static bool $isLazy = false;

    public static function canView():bool    {
        return auth()->user()?->hasPermissionTo('widget_StatDashboardNew');
    }

    protected function getCards(): array
    {
        return [
            Stat::make('عدد الطلاب', Student::count())
            ->description('32k increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
            ]),
            Stat::make('عدد المترشحين', Candidate::count()),
            Stat::make('عدد المعلمين', Teacher::count()),
            Stat::make('عدد الحلقات المفتوحة', Halaka::where('halaka_status', true)->count()),
            Stat::make('عدد حصص التسميع  ', RecitationSession::count()),
           // Stat::make('عدد الطلاب المحولين إلى الحلقات المفتوحة', Student::whereNotNull('halaka_id')->count()),
        ];
    }
}
