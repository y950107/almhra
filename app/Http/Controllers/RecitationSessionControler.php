<?php

namespace App\Http\Controllers;

use App\Models\AlMaherRecitation;
use App\Models\AlMaqraaRecitation;
use App\Models\AlMutqinRecitation;
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
    public function download(Request $request)
    {

       /* $sumTargetPages = RecitationSession::getTotalTargetPages();
        $sumActualPages = RecitationSession::getTotalActualPages();
        $cumulativeData = RecitationSession::getTotalTargetPagesPerStudent();
        $cumulativeData1 = RecitationSession::getTotalActualPagesPerStudent();*/

        $sumTargetPages = 0;
        $sumActualPages = 0;
        $cumulativeData = 0;
        $cumulativeData1 = 0;

        $sumTotalTargetPages = 0;
        $sumToactualPages = 0;
        $timeRange = $request->input('time_range');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $currentMonth = Carbon::now()->locale('ar')->translatedFormat('F Y');
        $presentstatus = RecitationSession::getPresentationPercent();
        if ($timeRange === 'custom' && (!$startDate || !$endDate)) {
            return back()->withErrors(['error' => 'يرجى تحديد تاريخ البداية والنهاية للتقرير المخصص']);
        }


        $sessions = RecitationSession::query()
            ->when($timeRange === 'monthly', function ($query) {
                $query->whereBetween('session_date', [
                    Carbon::now()->startOfMonth()->toDateString(),
                    Carbon::now()->endOfMonth()->toDateString(),
                ]);
            })
            ->when($timeRange === 'yearly', function ($query) {
                $query->whereBetween('session_date', [
                    Carbon::now()->startOfYear()->toDateString(),
                    Carbon::now()->endOfYear()->toDateString(),
                ]);
            })
            ->when($timeRange === 'custom', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('session_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()]);
            })
            ->get();

        $html = View::make('pdf.recitation', compact('sessions', 'timeRange', 'startDate', 'endDate', 'currentMonth','sumTargetPages','sumActualPages','sumTotalTargetPages','sumToactualPages','presentstatus'))->render();

        $mpdf = new Mpdf([
            'tempDir'=>storage_path('tempdir'),
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

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'I')),
            'تقرير-حصص-التسميع.pdf'
        );
    }

    public function downloadAlmaqraaReport(Request $request)
    {



        $sumTargetPages = settings('maqraa_monthly_target',40);
        $sumActualPages = AlMaqraaRecitation::getTotalActualPages();

        $sumTotalTargetPages  = 40;
        $sumToactualPages = 50;

        $timeRange = $request->input('time_range');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $currentMonth = Carbon::now()->locale('ar')->translatedFormat('F Y');
        $presentstatus = RecitationSession::getPresentationPercent();
        if ($timeRange === 'custom' && (!$startDate || !$endDate)) {
            return back()->withErrors(['error' => 'يرجى تحديد تاريخ البداية والنهاية للتقرير المخصص']);
        }


        $sessions = AlMaqraaRecitation::query()
            ->when($timeRange === 'monthly', function ($query) {
                $query->whereHas('recitationSession',function ($query) {
                    $query->whereBetween('session_date', [
                        Carbon::now()->startOfMonth()->toDateString(),
                        Carbon::now()->endOfMonth()->toDateString(),
                    ]);
                });
            })
            ->when($timeRange === 'yearly', function ($query) {
                $query->whereHas('recitationSession',function ($query) {
                    $query->whereBetween('session_date', [
                        Carbon::now()->startOfYear()->toDateString(),
                        Carbon::now()->endOfYear()->toDateString(),
                    ]);
                });
            })
            ->when($timeRange === 'custom', function ($query) use ($startDate, $endDate) {
                $query->whereHas('recitationSession',function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('session_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()]);
                });
            })
            ->get();


        $html = View::make('pdf.recitation', compact('sessions', 'timeRange', 'startDate', 'endDate', 'currentMonth','sumTargetPages','sumActualPages','sumTotalTargetPages','sumToactualPages','presentstatus'))->render();

        $mpdf = new Mpdf([
            'tempDir'=>storage_path('tempdir'),
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

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'I')),
            'تقرير-حصص-التسميع.pdf'
        );
    }

    public function downloadMaqraaReport(Request $request)
    {
        $currentMonth = Carbon::now()->locale('ar')->translatedFormat('F Y');

        $timeRange = $request->input('time_range');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $maqraa_start_date = Carbon::parse(settings('maqraa_start_date', '2024-09-01'));
        $maqraa_end_date = Carbon::parse(settings('maqraa_end_date', '2025-06-01'));


        $sessions = AlMaqraaRecitation::query()
            ->with(['recitationSession.student'])
            ->whereHas('recitationSession', function ($query) use ($timeRange, $startDate, $endDate , $maqraa_start_date ,$maqraa_end_date) {
                if ($timeRange === 'monthly') {
                    $query->whereBetween('session_date', [
                        Carbon::now()->startOfMonth()->toDateString(),
                        Carbon::now()->endOfMonth()->toDateString(),
                    ]);
                } elseif ($timeRange === 'yearly') {
                    $query->whereBetween('session_date', [
                        $maqraa_start_date->toDateString(),
                        $maqraa_end_date->toDateString(),
                    ]);
                } elseif ($timeRange === 'custom') {
                    $query->whereBetween('session_date', [
                        Carbon::parse($startDate)->toDateString(),
                        Carbon::parse($endDate)->toDateString(),
                    ]);
                }

            })
            ->get();

        $totalSessionsCount = $sessions->count();


        // Group by student
        $grouped = $sessions->groupBy(fn($item) => $item->recitationSession->student_id);

        $statsPerStudent = [];
        $totalPages = 0;
        $totalTarget = 0;
        $totalCumulativePages = 0;
        $totalCumulativeTarget = 0;
        $totalAbsencesCount = 0;

        foreach ($grouped as $studentId => $recitations) {
            $absencesCount = $recitations->filter(function ($recitation) {
                return $recitation->recitationSession->present !== 'present';
            })->count();



            $presentRecitations = $recitations->filter(function ($recitation) {
                return $recitation->recitationSession->present === 'present';
            });

            $student = $recitations->first()->recitationSession->student;
            $monthlyTarget = (int) $student->monthly_target_pages ?? settings('maqraa_monthly_target', 40);

            // Sort by session date
            $sorted = $presentRecitations->sortBy(fn($item) => $item->recitationSession->session_date);

            // Get first and last recitations
            $first = $sorted->first();
            $last = $sorted->last();

            $pagesRead = $presentRecitations->sum('pages');
            $percentage = (int) round(($pagesRead / $monthlyTarget) * 100, 0);

            // Calculate cumulative for this student
            $studentStartDate = Carbon::parse($student->start_date);
            $nowEndOfMonth = Carbon::now();

            $startOfTracking = $studentStartDate->greaterThan($maqraa_start_date)
                ? $studentStartDate
                : $maqraa_start_date;

            if ($timeRange === 'yearly') {
                $endOfTracking = $maqraa_end_date;
            }
            else if ($timeRange === 'custom') {
                $endOfTracking = Carbon::parse($endDate);
            }
            else {
                $endOfTracking = $nowEndOfMonth->lessThan($maqraa_end_date)
                    ? $nowEndOfMonth
                    : $maqraa_end_date;
            }


            $cumulativeRecitations = AlMaqraaRecitation::whereHas('recitationSession', function ($query) use ($student,$startOfTracking,$endOfTracking) {
                $query->whereBetween('session_date', [
                        $startOfTracking->toDateString(),
                        $endOfTracking->toDateString(),
                ]
                )->where('student_id', $student->id)->where('present','=','present');
            })->get();

            $monthsBetween = (int) $startOfTracking->startOfMonth()->diffInMonths($endOfTracking->endOfMonth()) + 1;

            $cumulativeTarget = $monthsBetween * $monthlyTarget;
            $cumulativePages = $cumulativeRecitations->sum('pages');

            $cumulativePercentage = $cumulativeTarget > 0 ? (int) round(($cumulativePages / $cumulativeTarget) * 100, 0) : 0;

            $statsPerStudent[] = [
                'student_id' => $studentId,
                'student_name' => $student->full_name ?? '-',
                'teacher_name' => $recitations->first()->recitationSession->halaka->teacher->name ?? '-',
                'absences' => $absencesCount,
                'registration_month' => $studentStartDate->getTranslatedMonthName(),
                'start_surah_id' => $first->start_surah_id,
                'start_surah_name' => getSurahName($first->start_surah_id),
                'start_ayah_id' => $first->start_ayah_id,
                'end_surah_id' => $last->end_surah_id,
                'end_surah_name' => getSurahName($last->end_surah_id),
                'end_ayah_id' => $last->end_ayah_id,
                'pages_read' => $pagesRead,
                'monthly_target' => $monthlyTarget,
                'monthly_percentage' => $percentage,
                'cumulative_pages' => $cumulativePages,
                'cumulative_target' => $cumulativeTarget,
                'cumulative_percentage' => $cumulativePercentage,
            ];

            $totalPages += $pagesRead;
            $totalTarget += $monthlyTarget;
            $totalCumulativePages += $cumulativePages;
            $totalCumulativeTarget += $cumulativeTarget;
            $totalAbsencesCount += $absencesCount;
        }

        $overallStats = [
            'total_absences_percentage' => $totalSessionsCount > 0 ? (int) round(($totalAbsencesCount / $totalSessionsCount) * 100, 2) : 0,
            'total_pages' => $totalPages,
            'total_monthly_target' => $totalTarget,
            'total_monthly_percentage' => $totalTarget > 0 ? (int) round(($totalPages / $totalTarget) * 100, 2) : 0,
            'total_cumulative_pages' => $totalCumulativePages,
            'total_cumulative_target' => $totalCumulativeTarget,
            'total_cumulative_percentage' => $totalCumulativeTarget > 0 ? (int) round(($totalCumulativePages / $totalCumulativeTarget) * 100, 2) : 0,
        ];


        $html = View::make('pdf.recitation', compact( 'currentMonth','timeRange', 'startDate', 'endDate', 'overallStats','statsPerStudent'))->render();

        $mpdf = new Mpdf([
            'tempDir'=>storage_path('tempdir'),
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

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'I')),
            'تقرير-حصص-التسميع.pdf'
        );
    }

    public function downloadMahirReport(Request $request)
    {
        $currentMonth = Carbon::now()->locale('ar')->translatedFormat('F Y');

        $timeRange = $request->input('time_range');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $mahir_start_date = Carbon::parse(settings('mahir_start_date', '2024-09-01'));
        $mahir_end_date = Carbon::parse(settings('mahir_end_date', '2025-06-01'));


        $sessions = AlMaherRecitation::query()
            ->with(['recitationSession.student'])
            ->whereHas('recitationSession', function ($query) use ($timeRange, $startDate, $endDate , $mahir_start_date ,$mahir_end_date) {
                if ($timeRange === 'monthly') {
                    $query->whereBetween('session_date', [
                        Carbon::now()->startOfMonth()->toDateString(),
                        Carbon::now()->endOfMonth()->toDateString(),
                    ]);
                } elseif ($timeRange === 'yearly') {
                    $query->whereBetween('session_date', [
                        $mahir_start_date->toDateString(),
                        $mahir_end_date->toDateString(),
                    ]);
                } elseif ($timeRange === 'custom') {
                    $query->whereBetween('session_date', [
                        Carbon::parse($startDate)->toDateString(),
                        Carbon::parse($endDate)->toDateString(),
                    ]);
                }

            })
            ->get();

        $totalSessionsCount = $sessions->count();


        // Group by student
        $grouped = $sessions->groupBy(fn($item) => $item->recitationSession->student_id);

        $statsPerStudent = [];
        $totalPages = 0;
        $totalTarget = 0;
        $totalCumulativePages = 0;
        $totalCumulativeTarget = 0;
        $totalAbsencesCount = 0;

        foreach ($grouped as $studentId => $recitations) {
            $absencesCount = $recitations->filter(function ($recitation) {
                return $recitation->recitationSession->present !== 'present';
            })->count();



            $presentRecitations = $recitations->filter(function ($recitation) {
                return $recitation->recitationSession->present === 'present';
            });

            $student = $recitations->first()->recitationSession->student;
            $monthlyTarget = (int) $student->monthly_target_pages ?? settings('mahir_monthly_target', 40);

            // Sort by session date
            $sorted = $presentRecitations->sortBy(fn($item) => $item->recitationSession->session_date);

            // Get first and last recitations
            $first = $sorted->first();
            $last = $sorted->last();

            $pagesRead = $presentRecitations->sum('pages');
            $percentage = (int) round(($pagesRead / $monthlyTarget) * 100, 0);

            // Calculate cumulative for this student
            $studentStartDate = Carbon::parse($student->start_date);
            $nowEndOfMonth = Carbon::now();

            $startOfTracking = $studentStartDate->greaterThan($mahir_start_date)
                ? $studentStartDate
                : $mahir_start_date;

            if ($timeRange === 'yearly') {
                $endOfTracking = $mahir_end_date;
            }
            else if ($timeRange === 'custom') {
                $endOfTracking = Carbon::parse($endDate);
            }
            else {
                $endOfTracking = $nowEndOfMonth->lessThan($mahir_end_date)
                    ? $nowEndOfMonth
                    : $mahir_end_date;
            }


            $cumulativeRecitations = AlMaherRecitation::whereHas('recitationSession', function ($query) use ($student,$startOfTracking,$endOfTracking) {
                $query->whereBetween('session_date', [
                        $startOfTracking->toDateString(),
                        $endOfTracking->toDateString(),
                    ]
                )->where('student_id', $student->id)->where('present','=','present');
            })->get();

            $monthsBetween = (int) $startOfTracking->startOfMonth()->diffInMonths($endOfTracking->endOfMonth()) + 1;

            $cumulativeTarget = $monthsBetween * $monthlyTarget;
            $cumulativePages = $cumulativeRecitations->sum('pages');

            $cumulativePercentage = $cumulativeTarget > 0 ? (int) round(($cumulativePages / $cumulativeTarget) * 100, 0) : 0;

            $statsPerStudent[] = [
                'student_id' => $studentId,
                'student_name' => $student->full_name ?? '-',
                'teacher_name' => $recitations->first()->recitationSession->halaka->teacher->name ?? '-',
                'absences' => $absencesCount,
                'registration_month' => $studentStartDate->getTranslatedMonthName(),
                'start_surah_id' => $first->start_surah_id,
                'start_surah_name' => getSurahName($first->start_surah_id),
                'start_ayah_id' => $first->start_ayah_id,
                'end_surah_id' => $last->end_surah_id,
                'end_surah_name' => getSurahName($last->end_surah_id),
                'end_ayah_id' => $last->end_ayah_id,
                'pages_read' => $pagesRead,
                'monthly_target' => $monthlyTarget,
                'monthly_percentage' => $percentage,
                'cumulative_pages' => $cumulativePages,
                'cumulative_target' => $cumulativeTarget,
                'cumulative_percentage' => $cumulativePercentage,
            ];

            $totalPages += $pagesRead;
            $totalTarget += $monthlyTarget;
            $totalCumulativePages += $cumulativePages;
            $totalCumulativeTarget += $cumulativeTarget;
            $totalAbsencesCount += $absencesCount;
        }

        $overallStats = [
            'total_absences_percentage' => $totalSessionsCount > 0 ? (int) round(($totalAbsencesCount / $totalSessionsCount) * 100, 2) : 0,
            'total_pages' => $totalPages,
            'total_monthly_target' => $totalTarget,
            'total_monthly_percentage' => $totalTarget > 0 ? (int) round(($totalPages / $totalTarget) * 100, 2) : 0,
            'total_cumulative_pages' => $totalCumulativePages,
            'total_cumulative_target' => $totalCumulativeTarget,
            'total_cumulative_percentage' => $totalCumulativeTarget > 0 ? (int) round(($totalCumulativePages / $totalCumulativeTarget) * 100, 2) : 0,
        ];


        $html = View::make('pdf.recitation', compact('currentMonth','timeRange', 'startDate', 'endDate', 'overallStats','statsPerStudent'))->render();

        $mpdf = new Mpdf([
            'tempDir'=>storage_path('tempdir'),
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

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'I')),
            'تقرير-حصص-التسميع.pdf'
        );
    }

    public function downloadMutqinReport(Request $request)
    {
        $currentMonth = Carbon::now()->locale('ar')->translatedFormat('F Y');

        $timeRange = $request->input('time_range');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $mahir_start_date = Carbon::parse(settings('mutqin_start_date', '2024-09-01'));
        $mahir_end_date = Carbon::parse(settings('mutqin_end_date', '2025-06-01'));


        $sessions = AlMutqinRecitation::query()
            ->with(['recitationSession.student'])
            ->whereHas('recitationSession', function ($query) use ($timeRange, $startDate, $endDate , $mahir_start_date ,$mahir_end_date) {
                if ($timeRange === 'monthly') {
                    $query->whereBetween('session_date', [
                        Carbon::now()->startOfMonth()->toDateString(),
                        Carbon::now()->endOfMonth()->toDateString(),
                    ]);
                } elseif ($timeRange === 'yearly') {
                    $query->whereBetween('session_date', [
                        $mahir_start_date->toDateString(),
                        $mahir_end_date->toDateString(),
                    ]);
                } elseif ($timeRange === 'custom') {
                    $query->whereBetween('session_date', [
                        Carbon::parse($startDate)->toDateString(),
                        Carbon::parse($endDate)->toDateString(),
                    ]);
                }

            })
            ->get();

        $totalSessionsCount = $sessions->count();

        // Group by student
        $grouped = $sessions->groupBy(fn($item) => $item->recitationSession->student_id);

        $statsPerStudent = [];
        $mem_totalPages = 0;
        $rev_totalPages = 0;
        $totalTarget = 0;
        $totalAbsencesCount = 0;

        foreach ($grouped as $studentId => $recitations) {
            $absencesCount = $recitations->filter(function ($recitation) {
                return $recitation->recitationSession->present !== 'present';
            })->count();

            $presentRecitations = $recitations->filter(function ($recitation) {
                return $recitation->recitationSession->present === 'present';
            });

            $student = $recitations->first()->recitationSession->student;
            $monthlyTarget = (int) $student->monthly_target_pages > 0 ? settings('mutqin_monthly_target', 20) : 20;

            // Sort by session date
            $sorted = $presentRecitations->sortBy(fn($item) => $item->recitationSession->session_date);


            $firstMem = $sorted->first(fn($item) => !is_null($item->mem_pages));
            $lastMem = $sorted->reverse()->first(fn($item) => !is_null($item->mem_pages));

            $firstRev = $sorted->first(fn($item) => !is_null($item->rev_pages));
            $lastRev = $sorted->reverse()->first(fn($item) => !is_null($item->rev_pages));

            $mem_pagesRead = $presentRecitations->sum('mem_pages');

            $mem_percentage = (int) round(($mem_pagesRead / $monthlyTarget) * 100, 0);

            $rev_pagesRead = $presentRecitations->sum('rev_pages');
            $rev_percentage = (int) round(($rev_pagesRead / $monthlyTarget) * 100, 0);

            // Calculate cumulative for this student
            $studentStartDate = Carbon::parse($student->start_date);


            $statsPerStudent[] = [
                'student_id' => $studentId,
                'student_name' => $student->full_name ?? '-',
                'teacher_name' => $recitations->first()->recitationSession->halaka->teacher->name ?? '-',
                'absences' => $absencesCount,
                'registration_month' => $studentStartDate->getTranslatedMonthName(),
                'mem_start_surah_name' => $firstMem ? getSurahName($firstMem->mem_start_surah_id) : '-',
                'mem_start_ayah_id' => $firstMem?->mem_start_ayah_id ?? '-',
                'mem_end_surah_name' => $lastMem ? getSurahName($lastMem->mem_end_surah_id) : '-',
                'mem_end_ayah_id' => $lastMem?->mem_end_ayah_id ?? '-',
                'mem_pages_read' => $mem_pagesRead ?? 0,
                'mem_monthly_target' => $monthlyTarget ?? 0,
                'mem_monthly_percentage' => $mem_percentage ?? 0,

                'rev_start_surah_name' => $firstRev ? getSurahName($firstRev->rev_start_surah_id) : '-',
                'rev_start_ayah_id' => $firstRev?->rev_start_ayah_id ?? '-',
                'rev_end_surah_name' => $lastRev ? getSurahName($lastRev->rev_end_surah_id) : '-',
                'rev_end_ayah_id' => $lastRev?->rev_end_ayah_id ?? '-',
                'rev_pages_read' => $rev_pagesRead,
                'rev_monthly_target' => $monthlyTarget,
                'rev_monthly_percentage' => $rev_percentage,
            ];

            $mem_totalPages += $mem_pagesRead;
            $rev_totalPages += $rev_pagesRead;
            $totalTarget += $monthlyTarget;
            $totalAbsencesCount += $absencesCount;
        }

        $overallStats = [
            'total_absences_percentage' => $totalSessionsCount > 0 ? (int) round(($totalAbsencesCount / $totalSessionsCount) * 100, 2) : 0,
            'mem_total_pages' => $mem_totalPages,
            'mem_total_monthly_target' => $totalTarget,
            'mem_total_monthly_percentage' => $totalTarget > 0 ? (int) round(($mem_totalPages / $totalTarget) * 100, 2) : 0,
            'rev_total_pages' => $rev_totalPages,
            'rev_total_monthly_target' => $totalTarget,
            'rev_total_monthly_percentage' => $totalTarget > 0 ? (int) round(($rev_totalPages / $totalTarget) * 100, 2) : 0,
        ];


        $html = View::make('pdf.mutqin-recitation', compact( 'currentMonth','timeRange', 'startDate', 'endDate', 'overallStats','statsPerStudent'))->render();

        $mpdf = new Mpdf([
            'tempDir'=>storage_path('tempdir'),
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

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'I')),
            'تقرير-حصص-التسميع.pdf'
        );
    }
}
