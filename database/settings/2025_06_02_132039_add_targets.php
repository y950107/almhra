<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.maqraa_monthly_target', 40.0);
        $this->migrator->add('general.mutqin_monthly_target', 20.0);
        $this->migrator->add('general.mahir_monthly_target', 20.0);
    }
};
