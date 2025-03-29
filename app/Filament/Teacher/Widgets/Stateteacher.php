<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Halaka;
use App\Models\Student;
use App\Models\Candidate;
use App\Models\RecitationSession;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class Stateteacher extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('عدد الطلاب', function () {
                return Student::whereHas('teacher.user', function ($query) {
                    $query->where('id', auth()->id());
                })->count();
            })
                ->description('الطلاب تحت اشرافكم')
                ->descriptionIcon('icon-students')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                ]),
            Stat::make('المترشحين المحالين الى مقابلة ', function () {
                return candidate::where('evaluated', false)->whereHas('teacher.user', function ($query) {
                    return $query->where('id', auth()->id());
                })->count();
            })->description('مقابلات التقييم')
                ->descriptionIcon('icon-condidates')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                ]),

            Stat::make('عدد الحلقات المفتوحة', function () {
                return Halaka::whereHas('teacher.user', function ($query) {
                    $query->where('id', auth()->id());
                })
                    ->count();
            })
                ->description('الحلقات المفتوحة')
                ->descriptionIcon('icon-sessions')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                ]),

            Stat::make('عدد الحصص المنجزة', function () {
                return RecitationSession::whereHas('halaka.teacher.user', function ($query) {
                    $query->where('id', auth()->id());
                })
                ->count();
            })
            ->description('حصص التسميع')
                ->descriptionIcon('icon-reports')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                ]),


            // Stat::make('عدد حصص التسميع  ', RecitationSession::count()),
            // Stat::make('User ID', auth()->id())
            //     ->description('المستخدم الحالي'),
        ];
    }
}
