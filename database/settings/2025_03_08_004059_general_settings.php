<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.min_age', 10);
        $this->migrator->add('general.max_age', 50);
        $this->migrator->add('general.passing_percentage', 70.0);
        $this->migrator->add('general.students_per_group', 10);
        $this->migrator->add('general.send_notifications', true);
        $this->migrator->add('general.admin_email', 'admin@example.com');
        $this->migrator->add('general.enable_weekly_reports', true);
        $this->migrator->add('general.enable_monthly_reports', true);
        $this->migrator->add('general.enable_yearly_reports', true);
        $this->migrator->add('general.tajweed_weight', 40.0);
        $this->migrator->add('general.memorization_weight', 40.0);
        $this->migrator->add('general.voice_quality_weight', 20.0);
        $this->migrator->add('general.badges_enabled', true);
        $this->migrator->add('general.badges_levels', [
            ['level' => 'مبتدئ', 'required_points' => 10],
            ['level' => 'متوسط', 'required_points' => 30],
            ['level' => 'متقدم', 'required_points' => 50],
        ]);
        $this->migrator->add('general.badge_criteria', [
            ['criteria' => 'إتمام 5 صفحات بدون أخطاء', 'points' => 5],
            ['criteria' => 'إتمام 10 صفحات بدون أخطاء', 'points' => 10],
        ]);
        $this->migrator->add('general.auto_accept_students', false);
        $this->migrator->add('general.session_duration', 60);
        $this->migrator->add('general.max_pages_per_session', 5);
        $this->migrator->add('general.default_language', 'ar');
        $this->migrator->add('general.dark_mode', false);
        $this->migrator->add('general.reading_types', [
            'حفص عن عاصم', 
            'ورش عن نافع', 
            'قالون عن نافع'
        ]);
    }
};
