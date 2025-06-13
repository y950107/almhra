<?php

namespace App\Filament\Widgets;

use App\Models\AlMaherRecitation;
use App\Models\AlMaqraaRecitation;
use App\Models\AlMutqinRecitation;
use App\Models\Candidate;
use App\Models\Halaka;
use App\Models\RecitationSession;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;


class StatDashboardNew extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;


 /*   public static function canView(): bool
    {
        return auth()->user()?->hasPermissionTo('widget_StatDashboardNew');

    }*/

    protected function getCards(): array
    {

        return [
            Stat::make('عدد الطلاب', Student::count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                ]),
            Stat::make('عدد المترشحين', Candidate::count()),
            Stat::make('عدد المعلمين', Teacher::count()),


        ];
    }
}
