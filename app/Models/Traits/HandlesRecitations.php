<?php

namespace App\Models\Traits;

use App\Services\Moshaf_madina_Service;
use Carbon\Carbon;

trait HandlesRecitations
{


    public static function filterByDateRange(array $range)
    {
        return static::whereHas('recitationSession', fn ($q) =>
        $q->whereBetween('session_date', [$range[0], $range[1]])
        )->with(['recitationSession.student'])->get();
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


            $absences = $recitations->where(fn($r) => $r->recitationSession->present !== 'present')->count();
            $present = $recitations->where(fn($r) => $r->recitationSession->present === 'present');
            $avgScore = $present
                ->map(fn($recitation) => $recitation->recitationSession->evaluation_score)
                ->average();
            $pagesRead = $present->sum($settings['pages']);

            $first = $present->sortBy('recitationSession.session_date')->first();
            $last = $present->sortByDesc('recitationSession.session_date')->first();
            $progStart = max(Carbon::parse($student->start_date), $progStart);
            $cumulative = $student->calculateProgress($progStart,$progEnd,$settings['pages']);



            $stats[] = [
                'student_id' => $studentId,
                'student_name' => $student->full_name ?? '-',
                'teacher_name' => $recitations->first()->recitationSession->halaka->teacher->name ?? '-',
                'absences' => $absences,
                'registration_month' => Carbon::parse($student->start_date)->getTranslatedMonthName(),
                'avg_evaluation_score' => round($avgScore),
                'start_surah_id' => $first->start_surah_id ?? null,
                'start_surah_name' =>  $first->start_surah_id ? getSurahName($first->start_surah_id) : "",
                'start_ayah_id' => $first->start_ayah_id ?? null,
                'end_surah_id' => $last->end_surah_id ?? null,
                'end_surah_name' => $last->end_surah_id ? getSurahName($last->end_surah_id): "",
                'end_ayah_id' => $last->end_ayah_id ?? null,
                'pages_read' => $pagesRead,
                'monthly_target' =>  $monthlyTarget,
                'monthly_percentage' => $monthlyTarget > 0 ? round($pagesRead / $monthlyTarget * 100) : 0,
                ...$cumulative,
            ];
        }

        return $stats;
    }

    public static function summarizeStats(array $stats): array
    {
        $totalPages = array_sum(array_column($stats, 'pages_read'));
        $totalTarget = array_sum(array_column($stats, 'monthly_target'));
        $totalCumulativePages = array_sum(array_column($stats, 'cumulative_pages'));
        $totalCumulativeTarget = array_sum(array_column($stats, 'cumulative_target'));
        $totalAbsences = array_sum(array_column($stats, 'absences'));
        $totalScores = array_sum(array_column($stats, 'avg_evaluation_score'));
        $totalSessions = count($stats);

        return [
            'total_absences_percentage' => $totalSessions > 0 ? round($totalAbsences / $totalSessions * 100) : 0,
            'total_pages' => $totalPages,
            'total_monthly_target' => $totalTarget,
            'total_monthly_percentage' => $totalTarget > 0 ? round($totalPages / $totalTarget * 100) : 0,
            'total_score' => $totalSessions > 0 ? round($totalScores / $totalSessions) : 0,
            'total_cumulative_pages' => $totalCumulativePages,
            'total_cumulative_target' => $totalCumulativeTarget,
            'total_cumulative_percentage' => $totalCumulativeTarget > 0 ? round($totalCumulativePages / $totalCumulativeTarget * 100) : 0,
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




}


