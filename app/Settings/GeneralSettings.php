<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public int $min_age;
    public int $max_age;

    public int $passing_percentage;
    public int $students_per_group;

    public bool $send_notifications;
    public string $admin_email;

    public bool $enable_weekly_reports;
    public bool $enable_monthly_reports;
    public bool $enable_yearly_reports;

    public float $tajweed_weight;
    public float $memorization_weight;
    public float $voice_quality_weight;

    public bool $badges_enabled;
    public array $badges_levels;
    public array $badge_criteria;

    public bool $auto_accept_students;

    public int $session_duration;
    public int $max_pages_per_session;

    public string $default_language;
    public bool $dark_mode;
    public array $reading_types;

    public array $ijaza_types = [];

    public array $qualifications = [];

    public string $company_name;
    public string $branch_name;
    public string $company_manager;
    public string $branch_manager;
    public string $phone_number;
    public string $contact_email;

    public string $logo = "logo.png";
    public string $favicon="favicon.png";

    public string $calendar_type;
    public array $working_days;
    public array $prayer_times;

    public static function group(): string
    {
        return 'general';
    }


}
