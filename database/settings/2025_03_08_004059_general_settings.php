<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // ✅ إعدادات الفئة العمرية
        $this->migrator->add('general.min_age', 10);
        $this->migrator->add('general.max_age', 50);
    
        // ✅ معدل القبول وعدد الطلاب
        $this->migrator->add('general.passing_percentage', 70.0);
        $this->migrator->add('general.students_per_group', 10);
        $this->migrator->add('general.auto_accept_students', false);
    
        // ✅ إعدادات البريد الإلكتروني
        $this->migrator->add('general.send_notifications', true);
        $this->migrator->add('general.admin_email', 'admin@example.com');
    
        // ✅ إعدادات التقارير
        $this->migrator->add('general.enable_weekly_reports', true);
        $this->migrator->add('general.enable_monthly_reports', true);
        $this->migrator->add('general.enable_yearly_reports', true);
    
        // ✅ الأوزان ومعايير التقييم
        $this->migrator->add('general.tajweed_weight', 40.0);
        $this->migrator->add('general.memorization_weight', 40.0);
        $this->migrator->add('general.voice_quality_weight', 20.0);
    
        // ✅ إعدادات الأوسمة
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
    
        // ✅ إعدادات الجلسات
        $this->migrator->add('general.session_duration', 60);
        $this->migrator->add('general.max_pages_per_session', 5);
    
        // ✅ إعدادات اللغة والوضع الليلي
        $this->migrator->add('general.default_language', 'ar');
        $this->migrator->add('general.dark_mode', false);
    
        // ✅ إعدادات القراءات
        $this->migrator->add('general.reading_types', [
            'حفص عن عاصم', 
            'ورش عن نافع', 
            'قالون عن نافع'
        ]);
    
        // ✅ إعدادات المنشأة
        $this->migrator->add('general.company_name', 'اسم المنشأة هنا');
        $this->migrator->add('general.branch_name', 'اسم الفرع هنا');
        $this->migrator->add('general.company_manager', 'مدير المنشأة');
        $this->migrator->add('general.branch_manager', 'مدير الفرع');
    
        // ✅ إعدادات الاتصال
        $this->migrator->add('general.phone_number', '0123456789');
        $this->migrator->add('general.contact_email', 'contact@example.com');
        
        // ✅ إعدادات الصور (الشعار والأيقونة)
        $this->migrator->add('general.logo', 'path/to/logo.png'); 
        $this->migrator->add('general.favicon', 'path/to/favicon.ico');
    
        // ✅ إعدادات الوقت وأيام الأسبوع
        $this->migrator->add('general.calendar_type', 'hijri'); // 'gregorian' or 'hijri'
        $this->migrator->add('general.working_days', [
            'saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday'
        ]);
    
        // ✅ إعدادات أوقات الصلاة
        $this->migrator->add('general.prayer_times', [
            ['prayer_name' => 'الفجر', 'prayer_time' => '05:00'],
            ['prayer_name' => 'الظهر', 'prayer_time' => '12:30'],
            ['prayer_name' => 'العصر', 'prayer_time' => '15:45'],
            ['prayer_name' => 'المغرب', 'prayer_time' => '18:15'],
            ['prayer_name' => 'العشاء', 'prayer_time' => '20:00'],
        ]);
    }
    
};
