<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class Moshaf_madina_Service
{
    protected $quranData;

    public function __construct()
    {
        $this->loadQuranData();
    }

    private function loadQuranData()
    {
        $path = database_path('data/Moshaf_Madina.json');
 
        if (!File::exists($path)) {
            throw new \Exception("ملف القرآن غير موجود في $path");
        }

        $this->quranData = json_decode(File::get($path), true);
    }

    public function getSurahs()
    {
        return collect($this->quranData)->unique('sura_no')
            ->map(fn($s) => ['id' => $s['sura_no'], 'name' => $s['sura_name_ar']])
            ->toArray();
    }
    public function getSurahName($surahNumber)
{
    $surah = collect($this->quranData)
        ->first(fn($s) => $s['sura_no'] == $surahNumber);

    return $surah ? $surah['sura_name_ar'] : 'غير معروف';
}

    public function getAyahs($surahNumber)
    {
        return collect($this->quranData)
            ->where('sura_no', $surahNumber)
            ->map(fn($a) => [
                'number' => $a['aya_no'],
                'text' => $a['aya_text_emlaey']
            ])
            ->toArray();
    }

    public function getStartPageByAyah($surahNumber, $ayahNumber)
    {
        $ayah = collect($this->quranData)
            ->first(fn($a) => $a['sura_no'] == $surahNumber && $a['aya_no'] == $ayahNumber);
    
        return $ayah ? $ayah['page'] : null;
    }

    public function calculateLines($startSurah, $startAyah, $endSurah, $endAyah)
    {
        return collect($this->quranData)
            ->filter(function ($ayah) use ($startSurah, $startAyah, $endSurah, $endAyah) {

                if ($startSurah == $endSurah) {
                    return $ayah['sura_no'] == $startSurah &&
                        $ayah['aya_no'] >= $startAyah &&
                        $ayah['aya_no'] <= $endAyah;
                }

                return ($ayah['sura_no'] == $startSurah && $ayah['aya_no'] >= $startAyah) ||
                    ($ayah['sura_no'] == $endSurah && $ayah['aya_no'] <= $endAyah) ||
                    ($ayah['sura_no'] > $startSurah && $ayah['sura_no'] < $endSurah);
            })
            ->groupBy('page')
            ->map(function ($pageAyahs) {
                return $pageAyahs->flatMap(function ($ayah) {
                    return range($ayah['line_start'], $ayah['line_end']);
                })->unique()->count();
            })
            ->sum();
    }
}










// {{-- <div class="logo">
//     <img src="{{ public_path('storage/images/mahara.png') }}" alt="شعار المنشأة">
// </div>

// <div class="title">
//     <h1>تقرير حصص التسميع</h1>
//     <h2>
//         {{ $timeRange === 'monthly' ? 'التقرير الشهري' : ($timeRange === 'yearly' ? 'التقرير السنوي' : 'تقرير مخصص من ' . $startDate . ' إلى ' . $endDate) }}
//     </h2>
//     <p>{{ now()->format('Y-m-d') }}</p>
// </div>

// <div class="logo">
//     <img src="{{ public_path('storage/images/mahara.png') }}" alt="شعار المنشأة">
// </div> --}}