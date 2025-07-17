<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use App\Settings\GeneralSettings;
use App\Services\Moshaf_madina_Service;

trait HandlesRecitations
{


    public static function filterByDateRange(array $range)
    {
        return static::whereHas('recitationSession', fn ($q) =>
        $q->whereBetween('session_date', [$range[0], $range[1]])
        )->with(['recitationSession.student']);
    }

    public static function forStudentWithin(array $range, int $studentId)
    {
        return static::whereHas('recitationSession', fn ($q) =>
        $q->whereBetween('session_date', [$range[0], $range[1]])
            ->where('student_id', $studentId)
            ->where('present', 'present')
        )->get();
    }


    public static function getStatsForGroupedSessions($grouped, Carbon $progStart, Carbon $progEnd, bool $mem = true)
    {
        $stats = [];

        foreach ($grouped as $studentId => $recitations) {
            $student = $recitations->first()->recitationSession->student;
            $settings = $student->getProgramSettings($mem);
            $monthlyTarget = $settings['monthly_target'];
            // Group recitations by lesson_title first
            $lastLessonInfo = null;
            $latestDate = null;
            $recitationsByLesson = $recitations->groupBy('lesson_title');

            $absences = $recitations->where(fn($r) => $r->recitationSession->present !== 'present')->count();
            $present = $recitations->where(fn($r) => $r->recitationSession->present === 'present');
            $avgScore = $present
                ->map(fn($recitation) => $recitation->recitationSession->evaluation_score)
                ->average();
            $pagesRead = $present->sum($settings['pages']);
            
            $first = $present->sortBy('recitationSession.session_date')->first();
            $last = $present->sortByDesc('recitationSession.session_date')->first();

            // $progStart = max(Carbon::parse($student->start_date), $progStart);
            $startDate = \Carbon\Carbon::parse($student->start_date);

            $cumulative = $student->calculateProgress($startDate,$progEnd,$settings['pages']);

            
            // Calculate mem_lines sum by lesson type
            
            $memLinesByLesson = [];
            $last_lesson_total_mem_lines =0;
            foreach ($recitationsByLesson as $lessonTitle => $lessonRecitations) {
                $presentLessonRecitations = $lessonRecitations->where(
                    fn($r) => $r->recitationSession->present === 'present'
                );
                
                $lessonLatestDate = $presentLessonRecitations->max(
                    fn($r) => $r->recitationSession->session_date
                );
                
                if ($lessonLatestDate && (!$latestDate || $lessonLatestDate > $latestDate)) {
                    $latestDate = $lessonLatestDate;
                    $lastLessonInfo = [
                        'title' => $presentLessonRecitations->first()?->translated_lesson_title ?? '',
                        'total_lines' => $presentLessonRecitations->sum('mem_lines'),
                        'date' => $lessonLatestDate
                    ];
                    $last_lesson_total_mem_lines = $presentLessonRecitations->sum('mem_lines');
                }
            }
       
            $mem_lines_sum = $present->sum('mem_lines');
            // dd($memLinesByLesson);
            $stats[] = [
                'student_id' => $studentId,
                'student_name' => $student->full_name ?? '-',
                'teacher_name' => $recitations->first()->recitationSession->halaka->teacher->name ?? '-',
                'absences' => $absences,
                'registration_month' => Carbon::parse($student->start_date)->getTranslatedMonthName(),
                'student_start_date' => $student->start_date,
                'avg_evaluation_score' => round($avgScore,1),
                'start_surah_id' => $first?->start_surah_id ?? null,
                'start_surah_name' =>  $first?->start_surah_id ? getSurahName($first?->start_surah_id) : "",
                'start_ayah_id' => $first?->start_ayah_id ?? null,
                'end_surah_id' => $last?->end_surah_id ?? null,
                'end_surah_name' => $last?->end_surah_id ? getSurahName($last?->end_surah_id): "",
                'end_ayah_id' => $last?->end_ayah_id ?? null,
                'pages_read' => $pagesRead,
                'monthly_target' =>  $monthlyTarget,
                'monthly_percentage' => $monthlyTarget > 0 ? round($pagesRead / $monthlyTarget * 100,1) : 0,
                ...$cumulative,
                'translated_lesson_title' =>  $last?->translated_lesson_title,
                'mem_lines' =>  $mem_lines_sum,
                'last_lesson_total_mem_lines' =>  $last_lesson_total_mem_lines,
                // 'mem_lines' =>  $last?->mem_lines,
            ];
        }

        return $stats;
    }

    public static function summarizeStats(array $stats,string $report_type="maqraa"): array
    {
        $totalPages = array_sum(array_column($stats, 'pages_read'));
        $totalTarget = request()['time_range'] =="monthly" ? array_sum(array_column($stats, 'monthly_target')) : array_sum(array_column($stats, 'monthly_target')) * 12;
        $totalCumulativePages = array_sum(array_column($stats, 'cumulative_pages'));
        $totalCumulativeTarget = array_sum(array_column($stats, 'cumulative_target'));
        $totalAbsences = array_sum(array_column($stats, 'absences'));
        $totalScores = array_sum(array_column($stats, 'avg_evaluation_score'));
        $totalSessions = count($stats);
        //caclculate total_working_days by month
        
        // dd($working_days);

        $working_days = self::getWorkingDays($report_type);
        // dd($working_days);
        // $working_days =$report_type== "maqraa" ? count(app(GeneralSettings::class)->maqraa_end_date) : count(app(GeneralSettings::class)->mahir_study_days);
     
        

        return [
            'total_absences_percentage' => $totalSessions > 0 ? round($totalAbsences / $working_days * 31) : 0,
            'total_pages' => $totalPages,
            'total_monthly_target' => $totalTarget,
            'total_monthly_percentage' => $totalTarget > 0 ? round($totalPages / $totalTarget * 100,1) : 0,
            'total_score' => $totalSessions > 0 ? round($totalScores / $totalSessions,1) : 0,
            'total_cumulative_pages' => $totalCumulativePages,
            'total_cumulative_target' => $totalCumulativeTarget,
            'total_cumulative_percentage' => $totalCumulativeTarget > 0 ? round($totalCumulativePages / $totalCumulativeTarget * 100,1) : 0,
        ];
    }

    public static function getAyahText($surah_id , $ayah_id)
    {
        $quranService = app(Moshaf_madina_Service::class);
        $ayahs = $quranService->getAyahs($surah_id);
        return collect($ayahs)->where('number', $ayah_id)->first()['text'] ?? null;
    }

    public static function getSurahName($surah_id)
    {
        $quranService = app(Moshaf_madina_Service::class);
        $surahs = $quranService->getSurahs();
        return collect($surahs)->where('id', $surah_id)->first()['name'] ?? null;
    }
    public static function getWorkingDays($report_type="maqraa")
    {
        // $settings = $report_type == "maqraa" 
        // ? app(GeneralSettings::class)->maqraa_end_date 
        // : app(GeneralSettings::class)->mahir_study_days;
 
        $working_days_total =0;

        $currentMonth = now()->month; // Get current month (1-12)

        switch ($report_type) {
            case 'mutqin':
                $sessionsPerMonth = intval(app(GeneralSettings::class)->mutqin_sessions_per_month);
                $exceptions = array_filter(
                    app(GeneralSettings::class)->mutqin_except_months_sessions ?? [],
                    fn($e) => $e['month'] <= $currentMonth
                );
                $working_days_total = $sessionsPerMonth - array_sum(array_column($exceptions, 'count'));
                break;
        
            case 'mahir':
                $sessionsPerMonth = intval(app(GeneralSettings::class)->mahir_sessions_per_month);
                $exceptions = array_filter(
                    app(GeneralSettings::class)->mahir_except_months_sessions ?? [],
                    fn($e) => $e['month'] <= $currentMonth
                );
                $working_days_total = $sessionsPerMonth - array_sum(array_column($exceptions, 'count'));
                break;
        
            default: // maqraa
                $sessionsPerMonth = intval(app(GeneralSettings::class)->maqraa_sessions_per_month);
                $exceptions = array_filter(
                    app(GeneralSettings::class)->maqraa_except_months_sessions ?? [],
                    fn($e) => $e['month'] <= $currentMonth
                );
                $working_days_total = $sessionsPerMonth - array_sum(array_column($exceptions, 'count'));
                break;
        }
       
        if (request('time_range') == "yearly") {
            // For yearly reports: calculate months from current year start
            $working_months = now()->diffInMonths(now()->startOfYear(), true);
            
            // Convert to decimal (e.g. 6.5 months)
            $currentDay = now()->day;
            $daysInMonth = now()->daysInMonth;
            $fraction = $currentDay / $daysInMonth;
            $working_months = floor($working_months) + $fraction;
            
            // Calculate yearly target
            $working_days_total = round($working_months * $working_days_total, 1);
        } 
        //// if time is cusom 
        if (request('time_range') == "custom" && request('start_date') && request('end_date')) {
            $startDate = Carbon::parse(request('start_date'));
            $endDate = Carbon::parse(request('end_date'));
            
            // Calculate total working days in the custom range
            $working_days_total = 0;
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                if (!$currentDate->isFriday() && !$currentDate->isSaturday()) {
                    $working_days_total++;
                }
                $currentDate->addDay();
            }
            
            // Apply monthly exceptions for the period
            $exceptionMonths = match($report_type) {
                'mutqin' => app(GeneralSettings::class)->mutqin_except_months_sessions ?? [],
                'mahir' => app(GeneralSettings::class)->mahir_except_months_sessions ?? [],
                default => app(GeneralSettings::class)->maqraa_except_months_sessions ?? []
            };
            
            // Subtract exceptions for months in the custom range
            foreach ($exceptionMonths as $exception) {
                $exceptionMonth = $exception['month'];
                $exceptionYear = $startDate->year; // Assuming current year
                
                if ($exceptionMonth >= $startDate->month && $exceptionMonth <= $endDate->month) {
                    $working_days_total -= $exception['count'];
                }
            }
            
            // Ensure we don't go below zero
            $working_days_total = max(0, $working_days_total);
        }
        // Calculate working days based on time range
        if (request('time_range') == "monthly" && request()->has('month')) {
            $selectedMonth = (int) request('month'); // Ensure numeric month
            $currentDate = now();
            $currentMonth = $currentDate->month;
            
            // Get exception months (ensure it's an array)
            switch ($report_type) {
                case 'mutqin':
                        $exceptionMonths = app(GeneralSettings::class)->mutqin_except_months_sessions ?? [];
                        $working_days_total =intval(app(GeneralSettings::class)->mutqin_sessions_per_month);
                    break;
                case 'mahir':
                        $exceptionMonths = app(GeneralSettings::class)->mahir_except_months_sessions ?? [];
                        $working_days_total =intval(app(GeneralSettings::class)->mahir_sessions_per_month);
                    break;
                
                default:
                        $exceptionMonths = app(GeneralSettings::class)->maqraa_except_months_sessions ?? [];
                        $working_days_total =intval(app(GeneralSettings::class)->maqraa_sessions_per_month);
                    break;
            }
            
            
            // Find exception for selected month (using numeric comparison)
            $monthException = collect($exceptionMonths)->first(function ($item) use ($selectedMonth) {
                return (int) $item['month'] === $selectedMonth;
            });
            
            // Adjust working days if exception exists
            if ($monthException) {
                $working_days_total = max(0, $working_days_total - (int) $monthException['count']);
            }
            
            // For current month (before month end)
            if ($selectedMonth == $currentMonth && !$currentDate->isLastOfMonth()) {
                $actual_working_days_total = 0;
                $startDate = Carbon::create($currentDate->year, $currentMonth, 1);
                $endDate = $currentDate;
                
                // Calculate actual working days so far
                while ($startDate <= $endDate) {
                    if (!$startDate->isFriday() && !$startDate->isSaturday()) {
                        $actual_working_days_total++;
                    }
                    $startDate->addDay();
                }
                
                // Take the minimum between scheduled and actual days
                $working_days_total = min($actual_working_days_total, $working_days_total);
            }
        }
        return $working_days_total;
    }




}


