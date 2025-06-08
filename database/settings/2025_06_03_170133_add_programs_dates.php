<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.maqraa_start_date', '2024-09-01');
        $this->migrator->add('general.maqraa_end_date', '2025-06-30');

        $this->migrator->add('general.mutqin_start_date', '2024-09-01');
        $this->migrator->add('general.mutqin_end_date', '2025-06-30');

        $this->migrator->add('general.mahir_start_date', '2024-09-01');
        $this->migrator->add('general.mahir_end_date', '2025-06-30');
    }
};
