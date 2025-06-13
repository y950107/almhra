<?php

namespace App\Http\Controllers;

use App\Models\RecitationSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;

class RecitationSessionControler extends Controller
{

    public function index()
    {
        $sessions = RecitationSession::with(['student'])->get();
        $cumulativeData = RecitationSession::getTotalTargetPagesPerStudent();

        return view('pdf.recitation', compact('sessions', 'cumulativeData'));
    }


    public function preview()
    {
        $recitation = RecitationSession::all();
        $html = View::make('pdf.recitation', compact('recitation'))->render();

        return response($html);
    }

    public function downloadMaqraaReport(Request $request)
    {
        $program = 'maqraa';
        $model = \App\Models\AlMaqraaRecitation::class;

        $settings = $this->getProgramSettings($program);
        $dateRange = $this->getDateRange($request, $settings['start'], $settings['end']);

        $sessions = $this->getFilteredSessions($model, $dateRange);
        $grouped = $sessions->groupBy(fn($item) => $item->recitationSession->student_id);

        $summary = $this->calculateStudentStats($grouped, $model, $dateRange, $settings['start'], $settings['end'], $program);

        $html = View::make('pdf.recitation', [
            'currentMonth' => now()->locale('ar')->translatedFormat('F Y'),
            'timeRange' => $request->input('time_range'),
            'startDate' => $request->input('start_date'),
            'endDate' => $request->input('end_date'),
            'overallStats' => $summary['overall'],
            'statsPerStudent' => $summary['students'],
            'program_name' => "برنامج المقراة"
        ])->render();


        $pdf = new \Mpdf\Mpdf([
            'tempDir' => storage_path('tempdir'),
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'Cairo',
            'dpi' => 300,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 5,
            'margin_right' => 5,
            'shrink_tables_to_fit' => 1,
        ]);

        $pdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($pdf->Output('', 'I')),
            'تقرير-حصص-التسميع.pdf'
        );
    }

    public function downloadMahirReport(Request $request)
    {
        $program = 'mahir';
        $model = \App\Models\AlMaherRecitation::class;

        $settings = $this->getProgramSettings($program);
        $dateRange = $this->getDateRange($request, $settings['start'], $settings['end']);

        $sessions = $this->getFilteredSessions($model, $dateRange);
        $grouped = $sessions->groupBy(fn($item) => $item->recitationSession->student_id);

        $summary = $this->calculateStudentStats($grouped, $model, $dateRange, $settings['start'], $settings['end'], $program);

        $html = View::make('pdf.recitation', [
            'currentMonth' => now()->locale('ar')->translatedFormat('F Y'),
            'timeRange' => $request->input('time_range'),
            'startDate' => $request->input('start_date'),
            'endDate' => $request->input('end_date'),
            'overallStats' => $summary['overall'],
            'statsPerStudent' => $summary['students'],
            'program_name' => "برنامج الماهر"
        ])->render();

        $pdf = new \Mpdf\Mpdf([
            'tempDir' => storage_path('tempdir'),
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'Cairo',
            'dpi' => 300,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 5,
            'margin_right' => 5,
            'shrink_tables_to_fit' => 1,
        ]);

        $pdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($pdf->Output('', 'I')),
            'تقرير-حصص-التسميع.pdf'
        );
    }


    public function downloadMutqinReport(Request $request)
    {
        $program = 'mutqin';
        $model = \App\Models\AlMutqinRecitation::class;

        $settings = $this->getProgramSettings($program);
        $settings['mem_monthly_target'] =  (int) settings("mutqin_mem_monthly_target", 40);
        $settings['rev_monthly_target'] =  (int) settings("mutqin_rev_monthly_target", 40);


        $dateRange = $this->getDateRange($request, $settings['start'], $settings['end']);

        $sessions = $this->getFilteredSessions($model, $dateRange);
        $grouped = $sessions->groupBy(fn($item) => $item->recitationSession->student_id);

        $summary = $this->calculateMutqinStats(
            $grouped,
            $settings['mem_monthly_target'],
            $settings['rev_monthly_target']
        );

        $html = View::make('pdf.mutqin-recitation', [
            'currentMonth' => now()->locale('ar')->translatedFormat('F Y'),
            'timeRange' => $request->input('time_range'),
            'startDate' => $request->input('start_date'),
            'endDate' => $request->input('end_date'),
            'overallStats' => $summary['overall'],
            'statsPerStudent' => $summary['students'],
            'program_name' => "برنامج المتقن"
        ])->render();

        $pdf = new \Mpdf\Mpdf([
            'tempDir' => storage_path('tempdir'),
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'Cairo',
            'dpi' => 300,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 5,
            'margin_right' => 5,
            'shrink_tables_to_fit' => 1,
        ]);

        $pdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($pdf->Output('', 'I')),
            'تقرير-حصص-التسميع.pdf'
        );
    }

    private function calculateMutqinStats($grouped, int $defaultMemTarget, int $defaultRevTarget): array
    {
        $stats = [];
        $memPages = $revPages = $memTargets = $revTargets = $absences = 0;
        $totalSessions = $grouped->flatten(1)->count();

        foreach ($grouped as $studentId => $recitations) {
            $student = $recitations->first()->recitationSession->student;

            $memTarget = (int) ($student->monthly_target_pages ?? $defaultMemTarget);
            $revTarget = (int) ($student->monthly_target_pages ?? $defaultRevTarget);

            $present = $recitations->where(fn($r) => $r->recitationSession->present === 'present');
            $absent = $recitations->where(fn($r) => $r->recitationSession->present !== 'present');

            $sorted = $present->sortBy('recitationSession.session_date');

            $firstMem = $sorted->first(fn($r) => $r->mem_pages !== null);
            $lastMem  = $sorted->reverse()->first(fn($r) => $r->mem_pages !== null);

            $firstRev = $sorted->first(fn($r) => $r->rev_pages !== null);
            $lastRev  = $sorted->reverse()->first(fn($r) => $r->rev_pages !== null);

            $memRead = $present->sum('mem_pages');
            $revRead = $present->sum('rev_pages');

            $stats[] = [
                'student_id' => $studentId,
                'student_name' => $student->full_name ?? '-',
                'teacher_name' => $recitations->first()->recitationSession->halaka->teacher->name ?? '-',
                'absences' => $absent->count(),
                'registration_month' => Carbon::parse($student->start_date)->getTranslatedMonthName(),

                'mem_start_surah_name' => $firstMem ? getSurahName($firstMem->mem_start_surah_id) : '-',
                'mem_start_ayah_id' => $firstMem?->mem_start_ayah_id ?? '-',
                'mem_end_surah_name' => $lastMem ? getSurahName($lastMem->mem_end_surah_id) : '-',
                'mem_end_ayah_id' => $lastMem?->mem_end_ayah_id ?? '-',
                'mem_pages_read' => $memRead,
                'mem_monthly_target' => $memTarget,
                'mem_monthly_percentage' => $memTarget > 0 ? (int) round($memRead / $memTarget * 100) : 0,

                'rev_start_surah_name' => $firstRev ? getSurahName($firstRev->rev_start_surah_id) : '-',
                'rev_start_ayah_id' => $firstRev?->rev_start_ayah_id ?? '-',
                'rev_end_surah_name' => $lastRev ? getSurahName($lastRev->rev_end_surah_id) : '-',
                'rev_end_ayah_id' => $lastRev?->rev_end_ayah_id ?? '-',
                'rev_pages_read' => $revRead,

                'rev_monthly_target' => $revTarget,
                'rev_monthly_percentage' => $revTarget > 0 ? (int) round($revRead / $revTarget * 100) : 0,
            ];

            $memPages += $memRead;
            $revPages += $revRead;
            $memTargets += $memTarget;
            $revTargets += $revTarget;
            $absences += $absent->count();
        }

        return [
            'students' => $stats,
            'overall' => [
                'total_absences_percentage' => $totalSessions > 0 ? round($absences / $totalSessions * 100) : 0,

                'mem_total_pages' => $memPages,
                'mem_total_monthly_target' => $memTargets,
                'mem_total_monthly_percentage' => $memTargets > 0 ? round($memPages / $memTargets * 100) : 0,

                'rev_total_pages' => $revPages,
                'rev_total_monthly_target' => $revTargets,
                'rev_total_monthly_percentage' => $revTargets > 0 ? round($revPages / $revTargets * 100) : 0,
            ],
        ];

    }


    private function getProgramSettings(string $program): array
    {
        return [
            'start' => Carbon::parse(settings("{$program}_start_date", '2024-09-01')),
            'end' => Carbon::parse(settings("{$program}_end_date", '2025-06-01')),
            'monthly_target' => (int) settings("{$program}_monthly_target", 40),
        ];
    }

    private function getDateRange(Request $request, Carbon $defaultStart, Carbon $defaultEnd): array
    {
        return match ($request->input('time_range')) {
            'monthly' => [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ],
            'yearly' => [$defaultStart, $defaultEnd],
            'custom' => [
                Carbon::parse($request->input('start_date')),
                Carbon::parse($request->input('end_date')),
            ],
            default => [$defaultStart, $defaultEnd],
        };
    }

    private function getFilteredSessions(string $model, array $range)
    {
        return $model::query()
            ->with(['recitationSession.student'])
            ->whereHas('recitationSession', fn($q) => $q->whereBetween('session_date', [
                $range[0]->toDateString(), $range[1]->toDateString(),
            ]))
            ->get();
    }

    private function calculateStudentStats($grouped, string $model, array $range, Carbon $progStart, Carbon $progEnd, string $program): array
    {
        $stats = [];
        $totalPages = $totalTarget = $totalCumulativePages = $totalCumulativeTarget = $totalAbsences = 0;

        foreach ($grouped as $studentId => $recitations) {
            $student = $recitations->first()->recitationSession->student;
            $monthlyTarget = (int) ($student->monthly_target_pages ?? settings("{$program}_monthly_target", 40));
            $absences = $recitations->where(fn($r) => $r->recitationSession->present !== 'present')->count();
            $present = $recitations->where(fn($r) => $r->recitationSession->present === 'present');
            $pagesRead = $present->sum('pages');

            $first = $present->sortBy('recitationSession.session_date')->first();
            $last = $present->sortByDesc('recitationSession.session_date')->first();

            $trackingStart = max(Carbon::parse($student->start_date), $progStart);
            $trackingEnd = match (true) {
                request('time_range') === 'yearly' => $progEnd,
                request('time_range') === 'custom' => Carbon::parse(request('end_date')),
                default => min(now(), $progEnd),
            };

            $cumulativeSessions = $model::whereHas('recitationSession', fn($q) => $q
                ->whereBetween('session_date', [$trackingStart, $trackingEnd])
                ->where('student_id', $student->id)
                ->where('present', '=', 'present')
            )->get();

            $months = (int) ($trackingStart->startOfMonth()->diffInMonths($trackingEnd->endOfMonth())) + 1;
            $cumulativeTarget = $months * $monthlyTarget;
            $cumulativePages = $cumulativeSessions->sum('pages');

            $stats[] = [
                'student_id' => $studentId,
                'student_name' => $student->full_name ?? '-',
                'teacher_name' => $recitations->first()->recitationSession->halaka->teacher->name ?? '-',
                'absences' => $absences,
                'registration_month' => Carbon::parse($student->start_date)->getTranslatedMonthName(),
                'start_surah_id' => $first->start_surah_id ?? null,
                'start_surah_name' => getSurahName($first->start_surah_id ?? 0),
                'start_ayah_id' => $first->start_ayah_id ?? null,
                'end_surah_id' => $last->end_surah_id ?? null,
                'end_surah_name' => getSurahName($last->end_surah_id ?? 0),
                'end_ayah_id' => $last->end_ayah_id ?? null,
                'pages_read' => $pagesRead,
                'monthly_target' =>  $monthlyTarget,
                'monthly_percentage' => $monthlyTarget > 0 ? (int) round($pagesRead / $monthlyTarget * 100) : 0,
                'cumulative_pages' => $cumulativePages,
                'cumulative_target' =>  $cumulativeTarget,
                'cumulative_percentage' => $cumulativeTarget > 0 ? (int) round($cumulativePages / $cumulativeTarget * 100) : 0,
            ];

            $totalPages += $pagesRead;
            $totalTarget += $monthlyTarget;
            $totalCumulativePages += $cumulativePages;
            $totalCumulativeTarget += $cumulativeTarget;
            $totalAbsences += $absences;
        }

        return [
            'students' => $stats,
            'overall' => [
                'total_absences_percentage' => $grouped->flatten(1)->count() > 0
                    ? round($totalAbsences / $grouped->flatten(1)->count() * 100)
                    : 0,
                'total_pages' => $totalPages,
                'total_monthly_target' => $totalTarget,
                'total_monthly_percentage' => $totalTarget > 0 ? (int) round($totalPages / $totalTarget * 100) : 0,
                'total_cumulative_pages' => $totalCumulativePages,
                'total_cumulative_target' => $totalCumulativeTarget,
                'total_cumulative_percentage' => $totalCumulativeTarget > 0 ? (int) round($totalCumulativePages / $totalCumulativeTarget * 100) : 0,
            ]
        ];
    }

}
