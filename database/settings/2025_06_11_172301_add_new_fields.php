<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {

        $this->migrator->add('general.maqraa_target_percentage', 70);
        $this->migrator->add('general.mahir_target_percentage', 70);
        $this->migrator->add('general.mutqin_target_percentage', 70);

        $this->migrator->add('general.maqraa_study_days', []);
        $this->migrator->add('general.mahir_study_days', []);
        $this->migrator->add('general.mutqin_study_days', []);
    }
};
