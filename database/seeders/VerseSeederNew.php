<?php

namespace Database\Seeders;

use App\Models\Verse;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\File;

class VerseSeederNew extends Seeder
{
    // /**
    //  * Run the database seeds.
    //  */
    // public function run(): void
    // {
    //     $json = File::get(database_path('data/Moshaf_Madina.json'));
    //     $verses = json_decode($json, true);

        
    //     foreach ($verses as $verse) {
    //         Verse::create([
    //             'id' => $verse['id'],
    //             'jozz' => $verse['jozz'],
    //             'sura_no' => $verse['sura_no'], 
    //             'sura_name_en' => $verse['sura_name_en'],
    //             'sura_name_ar' => $verse['sura_name_ar'],
    //             'page' => $verse['page'],
    //             'line_start' => $verse['line_start'],
    //             'line_end' => $verse['line_end'],
    //             'aya_no' => $verse['aya_no'],
    //             'aya_text' => $verse['aya_text'],
    //             'aya_text_emlaey' => $verse['aya_text_emlaey'],
    //         ]);
    //     }

    //     $this->command->info('good as you want');
    // }
}
