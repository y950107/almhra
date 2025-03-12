<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class PrayerTimeService
{
    public static function getPrayerTimes($latitude, $longitude)
    {
        $url = "https://api.aladhan.com/v1/timings?latitude={$latitude}&longitude={$longitude}&method=2";

        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            return [
                ['prayer_name' => 'الفجر', 'prayer_time' => $data['data']['timings']['Fajr']],
                ['prayer_name' => 'الظهر', 'prayer_time' => $data['data']['timings']['Dhuhr']],
                ['prayer_name' => 'العصر', 'prayer_time' => $data['data']['timings']['Asr']],
                ['prayer_name' => 'المغرب', 'prayer_time' => $data['data']['timings']['Maghrib']],
                ['prayer_name' => 'العشاء', 'prayer_time' => $data['data']['timings']['Isha']],
            ];
        }

        return null;
    }
}

