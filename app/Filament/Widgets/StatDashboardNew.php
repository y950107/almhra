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
            Stat::make('نسبة الانجاز برنامج المقراة',function () {
                $maqraa_start_date = Carbon::parse(settings('maarqq_start_date', '2024-09-01'));
                $maqraa_end_date = Carbon::parse(settings('maaraa_end_date', '2025-06-01'));

                $monthlyTarget = (int) settings('maqraa_monthly_target', 40);

                $monthsBetween = (int) $maqraa_start_date->startOfMonth()->diffInMonths($maqraa_end_date->endOfMonth()) + 1;

                $cumulativeRecitations = AlMaqraaRecitation::whereHas('recitationSession', function ($query) use ($maqraa_start_date, $maqraa_end_date) {
                    $query->whereBetween('session_date', [
                            $maqraa_start_date->toDateString(),
                            $maqraa_end_date->toDateString(),
                        ]
                    )->where('present','=','present');
                })->get();

                $cumulativeTarget = $monthsBetween * $monthlyTarget;
                $cumulativePages = $cumulativeRecitations->sum('pages');

                $cumulativePercentage = $cumulativeTarget > 0 ? (int) round(($cumulativePages / $cumulativeTarget) * 100, 0) : 0;

                return '% ' .$cumulativePercentage ;
            }),
            Stat::make('نسبة الانجاز برنامج المتقن',function () {
                $mutqin_start_date = Carbon::parse(settings('mutqin_start_date', '2024-09-01'));
                $mutqin_end_date = Carbon::parse(settings('mutqin_end_date', '2025-06-01'));

                $monthlyTarget = (int) settings('mutqin_monthly_target', 40);

                $monthsBetween = (int) $mutqin_start_date->startOfMonth()->diffInMonths($mutqin_end_date->endOfMonth()) + 1;

                $cumulativeRecitations = AlMutqinRecitation::whereHas('recitationSession', function ($query) use ($mutqin_start_date,$mutqin_end_date) {
                    $query->whereBetween('session_date', [
                            $mutqin_start_date->toDateString(),
                            $mutqin_end_date->toDateString(),
                        ]
                    )->where('present','=','present');
                })->get();

                $cumulativeTarget = $monthsBetween * $monthlyTarget;
                $cumulativePages =(int) ($cumulativeRecitations->sum('mem_pages') + $cumulativeRecitations->sum('rev_pages') ) / 2;

                $cumulativePercentage = $cumulativeTarget > 0 ? (int) round(($cumulativePages / $cumulativeTarget) * 100, 0) : 0;

                return '% ' .$cumulativePercentage ;
            }),
            Stat::make('نسبة الانجاز برنامج الماهر',function () {
                $mahir_start_date = Carbon::parse(settings('mahir_start_date', '2024-09-01'));
                $mahir_end_date = Carbon::parse(settings('mahir_end_date', '2025-06-01'));

                $monthlyTarget = (int) settings('mahir_monthly_target', 40);

                $monthsBetween = (int) $mahir_start_date->startOfMonth()->diffInMonths($mahir_end_date->endOfMonth()) + 1;

                $cumulativeRecitations = AlMaherRecitation::whereHas('recitationSession', function ($query) use ($mahir_start_date,$mahir_end_date) {
                    $query->whereBetween('session_date', [
                            $mahir_start_date->toDateString(),
                            $mahir_end_date->toDateString(),
                        ]
                    )->where('present','=','present');
                })->get();

                $cumulativeTarget = $monthsBetween * $monthlyTarget;
                $cumulativePages = $cumulativeRecitations->sum('pages');

                $cumulativePercentage = $cumulativeTarget > 0 ? (int) round(($cumulativePages / $cumulativeTarget) * 100, 0) : 0;

                return '% ' .$cumulativePercentage ;
            })

        ];
    }
}
