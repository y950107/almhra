<?php

namespace App\Filament\Resources\SessionsResource\Widgets;

use App\Models\Candidate;
use App\Models\Halaka;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getCards(): array
    {
        $currentYear = now()->year; // تحديد السنة الحالية

        return [
            Stat::make('عدد الطلاب', Student::count())->icon('heroicon-o-user-group'),
            Stat::make('عدد المترشحين', Candidate::count())->icon('heroicon-o-users'),
            Stat::make('عدد المعلمين', Teacher::count())->icon('heroicon-o-user'),
            Stat::make('عدد الحلقات المفتوحة', Halaka::where('halaka_status', true)->count())
                ->icon('heroicon-o-collection'),
        ];
    }
}

