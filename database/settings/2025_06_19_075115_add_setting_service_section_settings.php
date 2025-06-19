<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.services_section', []);
        $this->migrator->add('general.features_section', []);
        $this->migrator->add('general.slider_title', '');
        $this->migrator->add('general.student_login_button_title', '');
        $this->migrator->add('general.teacher_login_button_title', '');
    }
};
