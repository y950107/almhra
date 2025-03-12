<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Verse;
use App\Models\Surah;

class VerseSeeder extends Seeder
{
    public function run(): void
    {
        $surahs = Surah::all(); // جلب جميع السور من قاعدة البيانات

        foreach ($surahs as $surah) {
            $url = "https://api.alquran.cloud/v1/surah/{$surah->surah_number}";
            $response = Http::get($url);

            if ($response->successful()) {
                foreach ($response->json()['data']['ayahs'] as $data) {
                    Verse::updateOrCreate(
                        [
                            'ayah_number' => $data['number'],
                            'surah_id' => $surah->id
                        ],
                        [
                            'text' => $data['text'],
                            'page_number' => $data['page'],
                        ]
                    );
                }
            }
        }
    }
}