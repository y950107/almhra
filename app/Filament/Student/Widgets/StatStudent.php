<?php

namespace App\Filament\Student\Widgets;

use App\Models\RecitationSession;
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
        $student = Student::where('user_id', auth()->id())->with('candidate')->firstOrFail();
        $program = $student->candidate->program_type;

        // Program settings
        $progStart = Carbon::parse(settings("{$program}_start_date", '2024-09-01'));
        $progEnd = Carbon::parse(settings("{$program}_end_date", '2025-06-01'));

        $modelMap = [
            'maqraa' => [
                'model' => \App\Models\AlMaqraaRecitation::class,
                'pageField' => 'pages',
            ],
            'mahir' => [
                'model' => \App\Models\AlMaherRecitation::class,
                'pageField' => 'pages',
            ],
            'mutqin' => [
                'model' => \App\Models\AlMutqinRecitation::class,
                'pageField' => ['mem_pages', 'rev_pages'],
            ],
        ];

        if (!isset($modelMap[$program])) {
            return [];
        }

        $modelClass = $modelMap[$program]['model'];
        $pageField = $modelMap[$program]['pageField'];
        $monthlyTarget = (int) ($student->monthly_target_pages ?? settings("{$program}_monthly_target", 40));

        // Track between student's entry and program end
        $trackingStart = max(Carbon::parse($student->start_date), $progStart);
        $trackingEnd = $progEnd;

        // Fetch recitations during the full program duration for the student
        $recitations = $modelClass::whereHas('recitationSession', fn($q) =>
        $q->whereBetween('session_date', [$trackingStart, $trackingEnd])
            ->where('student_id', $student->id)
            ->where('present', 'present')
        )->with('recitationSession')->get();

        // Page sum logic
        $cumulativePages = is_array($pageField)
            ? round($recitations->sum('mem_pages') + $recitations->sum('rev_pages'))
            : $recitations->sum($pageField);


        // Total months student has participated in the program
        $months = round( $trackingStart->startOfMonth()->diffInMonths($trackingEnd->endOfMonth())) + 1;

        $cumulativeTarget = $months * $monthlyTarget;

        $percentage = $cumulativeTarget > 0
            ? round(($cumulativePages / $cumulativeTarget) * 100)
            : 0;

        return [
            Stat::make('إجمالي الأوجه المحققة', $cumulativePages)
                ->description("نسبة الإنجاز {$percentage}%")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('إجمالي الأوجه المستهدفة', $cumulativeTarget)
                ->description('منذ بداية البرنامج')
                ->color('info'),

            Stat::make('عدد الحصص', $recitations->count()),
        ];
    }

}
