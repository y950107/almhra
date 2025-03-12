<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Surah;

class SurahSeeder extends Seeder
{
    public function run(): void
    {
        $url = "https://api.alquran.cloud/v1/surah";
        $response = Http::get($url);

        if ($response->successful()) {
            foreach ($response->json()['data'] as $data) {
                Surah::updateOrCreate(
                    ['surah_number' => $data['number']],
                    [
                        'name_arabic' => $data['name'],
                        'name_english' => $data['englishName'],
                        'ayah_count' => $data['numberOfAyahs'],
                        'revelation_type' => $data['revelationType'],
                    ]
                );
            }
        }
    }
}
