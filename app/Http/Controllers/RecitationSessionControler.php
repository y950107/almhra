<?php

namespace App\Http\Controllers;

use App\Models\AlMaherRecitation;
use App\Models\AlMaqraaRecitation;
use App\Models\AlMutqinRecitation;
use App\Models\Candidate;
use App\Models\RecitationSession;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class RecitationSessionControler extends Controller
{

    public function index()
    {
        $sessions = RecitationSession::with(['student'])->get();
        $cumulativeData = RecitationSession::getTotalTargetPagesPerStudent();
        $is_mahir = false;
        return view('pdf.recitation', compact('sessions', 'cumulativeData','is_mahir'));
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
        $program_name = Candidate::getProgramTypes()[$program];

        $dateRange = $this->getDateRange($request, $program);
        $timeRange = $request->input('time_range');
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];

        $teachersIds = $request->input('teachers') ?? [];

        $sessionsQuery = AlMaqraaRecitation::filterByDateRange($dateRange);

        if (!empty($teachersIds)) {
            $sessionsQuery->whereHas('recitationSession', function (Builder $query) use ($teachersIds) {
                $query->whereHas('halaka', function (Builder $query) use ($teachersIds) {
                    $query->whereIn('teacher_id', $teachersIds);
                });
            });
        }
       
        $sessions = $sessionsQuery->get();

        $grouped = $sessions->groupBy(fn($item) => $item->recitationSession->student_id);

        $stats = AlMaqraaRecitation::getStatsForGroupedSessions($grouped, $startDate, $endDate);
        $summary = AlMaqraaRecitation::summarizeStats($stats);

        $title = ($timeRange === 'monthly')
            ? 'التقرير الشهري'
            : (($timeRange === 'yearly')
                ? 'التقرير السنوي'
                : 'تقرير مخصص من ' . $startDate . ' إلى ' . $endDate);

        $title .= ' - ' . ($program_name ?? '');

        $html = View::make('pdf.recitation', [
            'title' => $title,
            'timeRange' => $timeRange,
            'overallStats' => $summary,
            'statsPerStudent' => $stats,
            'is_mahir' => false
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
        $program_name = Candidate::getProgramTypes()[$program];

        $dateRange = $this->getDateRange($request, $program);

        $timeRange = $request->input('time_range');
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];

        $teachersIds = $request->input('teachers');

        $sessionsQuery = AlMaherRecitation::filterByDateRange($dateRange);

        if (!empty($teachersIds)) {
            $sessionsQuery->whereHas('recitationSession', function (Builder $query) use ($teachersIds) {
                $query->whereHas('halaka', function (Builder $query) use ($teachersIds) {
                    $query->whereIn('teacher_id', $teachersIds);
                });
            });
        }

        $sessions = $sessionsQuery->get();
        $grouped = $sessions->groupBy(fn($item) => $item->recitationSession->student_id);

        $stats = AlMaherRecitation::getStatsForGroupedSessions($grouped, $startDate, $endDate);
        $summary = AlMaherRecitation::summarizeStats($stats,$report_type="mahir");


        $title = ($timeRange === 'monthly')
            ? 'التقرير الشهري'
            : (($timeRange === 'yearly')
                ? 'التقرير السنوي'
                : 'تقرير مخصص من ' . $startDate . ' إلى ' . $endDate);

        $title .= ' - ' . ($program_name ?? '');

        $html = View::make('pdf.recitation', [
            'title' => $title,
            'timeRange' => $timeRange,
            'overallStats' => $summary,
            'statsPerStudent' => $stats,
            'is_mahir' => true
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

    public function downloadMahirDetailedReport(Request $request)
    {

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $teachersIds = $request->input('teachers');


        $sessions = RecitationSession::query()->when($startDate, function (Builder $query) use($startDate) {
            $query->where('session_date','>=' , $startDate);
        })->when($endDate, function (Builder $query) use($endDate) {
            $query->where('session_date','<=' , $endDate);
        })->whereHas('almaherRecitation')->with(['almaherRecitation']);

        if (!empty($teachersIds)) {
            $sessions = $sessions->whereHas('halaka',function (Builder $query) use($teachersIds) {
                $query->whereIn('teacher_id',$teachersIds);
            })->get();
        }



        $html = View::make('pdf.mahir-detailed-recitation', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'sessions' => $sessions,
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
            'تقرير-برنامج الماهر.pdf'
        );
    }

    public function downloadMutqinReport(Request $request)
    {

        $program = 'mutqin';
        $program_name = Candidate::getProgramTypes()[$program];


        $dateRange = $this->getDateRange($request, $program);


        $teachersIds = $request->input('teachers');

        $sessionsQuery = AlMutqinRecitation::filterByDateRange($dateRange);

        if (!empty($teachersIds)) {
            $sessionsQuery->whereHas('recitationSession', function (Builder $query) use ($teachersIds) {
                $query->whereHas('halaka', function (Builder $query) use ($teachersIds) {
                    $query->whereIn('teacher_id', $teachersIds);
                });
            });
        }

        $sessions = $sessionsQuery->get();
        $grouped = $sessions->groupBy(fn($item) => $item->recitationSession->student_id);

        $summary = $this->calculateMutqinStats($grouped, $dateRange[0], $dateRange[1]);

        $timeRange = $request->input('time_range');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $title = ($timeRange === 'monthly')
            ? 'التقرير الشهري'
            : (($timeRange === 'yearly')
                ? 'التقرير السنوي'
                : 'تقرير مخصص من ' . $startDate . ' إلى ' . $endDate);

        $title .= ' - ' . ($program_name ?? '');


        $html = View::make('pdf.mutqin-recitation', [
            'title' => $title,
            'timeRange' => $timeRange,
            'overallStats' => $summary['overall'],
            'statsPerStudent' => $summary['students'],
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

    public function downloadMutqinDetailedReport(Request $request)
    {


        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $teachersIds = $request->input('teachers');

        $sessions = RecitationSession::query()
            ->when($startDate, function (Builder $query) use($startDate) {
            $query->where('session_date','>=' , $startDate);
        })->when($endDate, function (Builder $query) use($endDate) {
            $query->where('session_date','<=' , $endDate);
        })->whereHas('almutqinRecitation')->with(['almutqinRecitation']);

        if (!empty($teachersIds)) {
            $sessions = $sessions->whereHas('halaka',function (Builder $query) use($teachersIds) {
                $query->whereIn('teacher_id',$teachersIds);
            })->get();
        }

        $html = View::make('pdf.mutqin-detailed-recitation', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'sessions' => $sessions,
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
            'تقرير-برنامج المتقن.pdf'
        );
    }

    private function calculateMutqinStats($grouped, $progStart, $progEnd): array
    {
        $stats = [];
        $memPages = $revPages = $memTargets = $revTargets = $absences = $totalScores = 0;
        $totalSessions = $grouped->flatten(1)->count();

        foreach ($grouped as $studentId => $recitations) {
            $student = $recitations->first()->recitationSession->student;


            $present = $recitations->where(fn($r) => $r->recitationSession->present === 'present');
            $absent = $recitations->where(fn($r) => $r->recitationSession->present !== 'present');
            $avgScore = $present
                ->map(fn($recitation) => $recitation->recitationSession->evaluation_score)
                ->average();
            $sorted = $present->sortBy('recitationSession.session_date');

            $firstMem = $sorted->first(fn($r) => $r->mem_pages !== null);
            $lastMem = $sorted->reverse()->first(fn($r) => $r->mem_pages !== null);

            $firstRev = $sorted->first(fn($r) => $r->rev_pages !== null);
            $lastRev = $sorted->reverse()->first(fn($r) => $r->rev_pages !== null);


            $progStart = max(Carbon::parse($student->start_date), $progStart);

            $mem_progress = $student->calculateProgress($progStart, $progEnd, 'mem_pages', true);
            $rev_progress = $student->calculateProgress($progStart, $progEnd, 'rev_pages', false);


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
                'mem_pages_read' => $mem_progress['cumulative_pages'],
                'mem_monthly_target' => $mem_progress['cumulative_target'],
                'mem_monthly_percentage' => $mem_progress['cumulative_percentage'],
                'avg_evaluation_score' => $avgScore,
                'rev_start_surah_name' => $firstRev ? getSurahName($firstRev->rev_start_surah_id) : '-',
                'rev_start_ayah_id' => $firstRev?->rev_start_ayah_id ?? '-',
                'rev_end_surah_name' => $lastRev ? getSurahName($lastRev->rev_end_surah_id) : '-',
                'rev_end_ayah_id' => $lastRev?->rev_end_ayah_id ?? '-',
                'rev_pages_read' => $rev_progress['cumulative_pages'],
                'rev_monthly_target' => $rev_progress['cumulative_target'],
                'rev_monthly_percentage' => $rev_progress['cumulative_percentage'],
            ];

            $memPages += $mem_progress['cumulative_pages'];
            $memTargets += $mem_progress['cumulative_target'];

            $revPages += $rev_progress['cumulative_pages'];
            $revTargets += $rev_progress['cumulative_target'];

            $absences += $absent->count();
            $totalScores += $avgScore;
        }

        return [
            'students' => $stats,
            'overall' => [
                'total_absences_percentage' => $totalSessions > 0 ? round($absences / $totalSessions * 100) : 0,

                'mem_total_pages' => $memPages,
                'mem_total_monthly_target' => $memTargets,
                'mem_total_monthly_percentage' => $memTargets > 0 ? round($memPages / $memTargets * 100,1) : 0,
                'total_score' => $totalSessions > 0 ? round($totalScores / $totalSessions,1) : 0,
                'rev_total_pages' => $revPages,
                'rev_total_monthly_target' => $revTargets,
                'rev_total_monthly_percentage' => $revTargets > 0 ? round($revPages / $revTargets * 100,1) : 0,
            ],
        ];

    }


    private function getDateRange(Request $request, string $program): array
    {
        $timeRange = $request->input('time_range');
        
    
        if ($timeRange === 'monthly' && $month=$request->input('month')) {
            
            $year = $request->input('year', date('Y'));
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            
            return [Carbon::parse($startDate->toDateString()), Carbon::parse($endDate->toDateString())];
        }
        return match ($request->input('time_range')) {
            'monthly' => [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ],
            'yearly' => [
                Carbon::parse(settings("{$program}_start_date", '2024-09-01')),
                Carbon::parse(settings("{$program}_end_date", '2025-06-01'))
            ],
            'custom' => [
                Carbon::parse($request->input('start_date')),
                Carbon::parse($request->input('end_date')),
            ]
        };
    }


}
