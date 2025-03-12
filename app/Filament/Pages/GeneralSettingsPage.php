<?php

namespace App\Filament\Resources\SettingsResource\Pages;


use Tabs\Tab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\GeneralSettings;
use App\Services\PrayerTimeService;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;




class GeneralSettingsPage extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'الإعدادات العامة';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static string $settings = GeneralSettings::class;
    /*   public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user && method_exists($user, 'can') && $user->can('view_general_settings');
    } */
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->columns(2)
                ->schema([
                    Tabs::make('إعدادات النظام')
                        ->extraAttributes(['data-tabs-id' => 'settings-tabs'])
                        ->tabs([


                            Tabs\Tab::make('إعدادات الطلاب')->schema([
                                Section::make('الفئة العمرية')->schema([
                                    TextInput::make('min_age')->label('العمر الأدنى')->required()->numeric(),
                                    TextInput::make('max_age')->label('العمر الأقصى')->required()->numeric(),

                                ]),
                                Section::make('معدل القبول')->schema([
                                    TextInput::make('passing_percentage')->label('المعدل %')->required()->numeric(),
                                ]),
                                Section::make('العدد الاقصي للطلاب الملتحقين بالحلقة')->schema([
                                    TextInput::make('students_per_group')->label('عدد الطلاب في الحلقة')->required()->numeric(),
                                ]),

                                Section::make('القبول التلقائي')->schema([
                                    Toggle::make('auto_accept_students')->label('قبول ؟'),
                                ])
                            ]),

                            Tabs\Tab::make('إعدادات حلقات التسميع ')->schema([
                                Section::make()->schema([
                                    Repeater::make('reading_types')
                                        ->label(' القراءات')
                                        ->schema([
                                            TextInput::make('reading')->label('اسم القراءة')->placeholder('مثال: حفص عن عاصم')->required(),
                                        ])
                                        ->minItems(1)
                                        ->addable()
                                        ->reorderable()
                                        ->deletable(),
                                ]),

                                Section::make('الاوزان و معايير التقييم')->schema([
                                    TextInput::make('tajweed_weight')->label('وزن التجويد')->required()->numeric(),
                                    TextInput::make('memorization_weight')->label('وزن الحفظ')->required()->numeric(),
                                    TextInput::make('voice_quality_weight')->label('وزن جودة الصوت')->required()->numeric(),
                                ]),

                                Section::make('خطة التسميع')->schema([
                                    TextInput::make('session_duration')->label('مدة الجلسة بالدقائق')->required()->numeric(),
                                    TextInput::make('max_pages_per_session')->label('أقصى عدد للصفحات في الجلسة')->required()->numeric(),
                                ]),

                                Section::make('تحكم في التقارير')->schema([
                                    Toggle::make('enable_weekly_reports')->label('تفعيل التقارير الأسبوعية'),
                                    Toggle::make('enable_monthly_reports')->label('تفعيل التقارير الشهرية'),
                                    Toggle::make('enable_yearly_reports')->label('تفعيل التقارير السنوية'),
                                ])
                            ]),

                            Tabs\Tab::make('إعدادات الأوسمة')->schema([
                                Section::make()->schema([
                                    Toggle::make('badges_enabled')->label('تفعيل الأوسمة'),
                                    Repeater::make('badges_levels')
                                        ->label('مستويات الأوسمة')
                                        ->schema([
                                            TextInput::make('level')->label('اسم المستوى')->required(),
                                            TextInput::make('required_points')->label('النقاط المطلوبة')->required()->numeric(),
                                        ]),
                                    Repeater::make('badge_criteria')
                                        ->label('معايير الحصول على الأوسمة')
                                        ->schema([
                                            TextInput::make('criteria')->label('المعيار')->required(),
                                            TextInput::make('points')->label('النقاط')->required()->numeric(),
                                        ]),
                                ]),
                            ]),

                            Tabs\Tab::make('إعدادات عامة ')->schema([
                                Section::make('اشعارات البريد الالكتروني')->schema([
                                    Toggle::make('send_notifications')->label('تفعيل الإشعارات')->required(),
                                    TextInput::make('admin_email')->label('البريد الإلكتروني للمشرف')->required()->email(),
                                ]),
                                Section::make('اللغة')->schema([
                                    Select::make('default_language')->label('اللغة الافتراضية')
                                        ->options([
                                            'ar' => 'العربية',
                                            'en' => 'الإنجليزية',
                                        ])
                                        ->required(),

                                    Toggle::make('dark_mode')->label('تفعيل الوضع الليلي'),
                                ])
                            ]),
                            Tabs\Tab::make('إعدادات المنشأة')->schema([
                                Section::make('الشعار والأيقونة')->schema([
                                    FileUpload::make('logo')->label('شعار المنشأة')->image(),
                                    FileUpload::make('favicon')->label('الأيقونة المفضلة')->image(),
                                ]),
                                Section::make('معلومات المنشأة')->schema([
                                    TextInput::make('company_name')->label('اسم المنشأة')->required(),
                                    TextInput::make('branch_name')->label('اسم الفرع')->required(),
                                    TextInput::make('company_manager')->label('مدير المنشأة')->required(),
                                    TextInput::make('branch_manager')->label('مدير الفرع')->required(),
                                ]),
                                Section::make('معلومات الاتصال')->schema([
                                    TextInput::make('phone_number')->label('رقم الهاتف')->required()->tel(),
                                    TextInput::make('contact_email')->label('البريد الإلكتروني')->required()->email(),
                                ]),

                            ]),

                            // 🔹 إعدادات الوقت وأيام الأسبوع
                            Tabs\Tab::make('إعدادات الوقت')->schema([
                                Section::make('التقويم')->schema([
                                    Select::make('calendar_type')->label('نوع التقويم')
                                        ->options([
                                            'gregorian' => 'ميلادي',
                                            'hijri' => 'هجري',
                                        ])
                                        ->required(),
                                ]),
                                Section::make('أيام العمل')->schema([
                                    Repeater::make('working_days')
                                        ->label('أيام الأسبوع')
                                        ->schema([
                                            Select::make('day')->label('اليوم')
                                                ->options([
                                                    'saturday' => 'السبت',
                                                    'sunday' => 'الأحد',
                                                    'monday' => 'الإثنين',
                                                    'tuesday' => 'الثلاثاء',
                                                    'wednesday' => 'الأربعاء',
                                                    'thursday' => 'الخميس',
                                                    'friday' => 'الجمعة',
                                                ])
                                                ->required(),
                                        ])
                                        ->minItems(1)
                                        ->addable()
                                        ->reorderable()
                                        ->deletable(),
                                ]),
                                Section::make('أوقات الصلاة')->schema([
                                    Repeater::make('prayer_times')
        ->label('أوقات الصلاة')
        ->schema([
            TextInput::make('prayer_name')->label('اسم الصلاة')->required(),
            TextInput::make('prayer_time')->label('الوقت (HH:MM)')->required(),
        ])
        ->default(PrayerTimeService::getPrayerTimes(24.7136, 46.6753)),
                                ]),
                            ]),



                        ])->columnSpanFull()

                ])
        ]);
    }
}
