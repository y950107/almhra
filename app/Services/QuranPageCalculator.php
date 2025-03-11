<?php

namespace App\Services;

class QuranPageCalculator
{
    /**
     * Create a new class instance.
     */

    private static $quranData;

    public static function initialize()
    {
        //get data from Json file 
        self::$quranData = json_decode(file_get_contents(storage_path('quran_pages.json')), true);
    }

    public static function getPage(string $surahName, int $ayah): int
    {
        self::initialize();

        // looking fo surah name
        foreach (self::$quranData as $surah) {
            if ($surah['name'] === $surahName) {
                foreach ($surah['ayahs'] as $ayahData) {
                    if ($ayahData['ayah'] === $ayah) {
                        return $ayahData['page'];
                    }
                }
            }
        }

        return 0; //if nothing get 0;
    }

    public static function calculateProgress($session)
    {
        $startPage = self::getPage($session->start_surah, $session->start_ayah);
        $endPage = self::getPage($session->end_surah, $session->end_ayah);
        $actualPage = self::getPage($session->actual_end_surah, $session->actual_end_ayah);
        
        return round(($actualPage - $startPage) / ($endPage - $startPage) * 100, 2) . '%';
    }
    public function __construct()
    {
        //
    }
}
