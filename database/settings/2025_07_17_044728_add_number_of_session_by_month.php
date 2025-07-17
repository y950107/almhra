<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.maqraa_sessions_per_month', '');
        $this->migrator->add('general.maqraa_except_months_sessions', []); //holiday or something else
        $this->migrator->add('general.mutqin_sessions_per_month', '');
        $this->migrator->add('general.mutqin_except_months_sessions', ''); //holiday or something else
        $this->migrator->add('general.mahir_sessions_per_month', '');
        $this->migrator->add('general.mahir_except_months_sessions', []); //holiday or something else
    }
};
