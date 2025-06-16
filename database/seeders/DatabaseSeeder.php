<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            ShieldSeeder::class,
            SuperAdminSeeder::class,
            SurahSeeder::class,
            VerseSeeder::class,

        ]);
        /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */


    }
}
