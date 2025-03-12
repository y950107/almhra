<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class QuranService
{
    
    public static function getSurah($surahNumber)
    {
        $url = "https://api.quran.com/api/v4/chapters/{$surahNumber}";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json()['chapter'];
            return [
                'id' => $data['id'],
                'name' => $data['name_arabic'],
                'name_english' => $data['name_simple'],
                'verses_count' => $data['verses_count'],
                'pages' => $data['pages'][0] 
            ];
        }

        return null;
    }

    
    public static function getVerse($surahNumber, $verseNumber)
    {
        $url = "https://api.quran.com/api/v4/verses/by_chapter/{$surahNumber}?verse_key={$surahNumber}:{$verseNumber}";
        $response = Http::get($url);

        if ($response->successful()) {
            $verse = $response->json()['verses'][0];

            return [
                'id' => $verse['id'],
                'verse_key' => $verse['verse_key'],
                'verse_number' => $verse['verse_number'],
                'page_number' => $verse['page_number'],
            ];
        }

        return null;
    }

    
    public static function getVersesByPage($pageNumber)
    {
        $url = "https://api.quran.com/api/v4/verses/by_page/{$pageNumber}?language=ar&words=true";
        $response = Http::get($url);

        if ($response->successful()) {
            $verses = $response->json()['verses'];
            return collect($verses)->map(function ($ayah) {
                return [
                    'verse_key' => $ayah['verse_key'],
                    'verse_number' => $ayah['verse_number'],
                    'text' => collect($ayah['words'])->pluck('text')->implode(' '), // دمج الكلمات لتكوين نص الآية
                    'page_number' => $ayah['page_number'],
                ];
            })->toArray();
        }

        return null;
    }
}
