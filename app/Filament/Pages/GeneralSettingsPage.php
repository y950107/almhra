<?php

namespace App\Filament\Pages;



use App\Settings\GeneralSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Tabs\Tab;


class GeneralSettingsPage extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'الإعدادات العامة';

    protected static ?string $title = 'الإعدادات العامة';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static string $settings = GeneralSettings::class;
    public static function getNavigationLabel(): string
    {
        return __('filament.general_settings');
    }
    public static function getModelLabel(): string
    {
        return __('filament.general_settings');
    }

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
        return $form
            ->schema([
                Section::make()

                    ->schema([

                        Tabs::make('Tabs')->persistTabInQueryString()
                            ->tabs([
                                Tabs\Tab::make('اعدادات الحصص')
                                    ->icon('icon-settings')
                                    ->schema([
                                        Forms\Components\Section::make('إعدادات عامة')
                                            ->schema([
                                                Forms\Components\TextInput::make('min_age')
                                                    ->label('العمر الأدنى')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\TextInput::make('max_age')
                                                    ->label('العمر الأقصى')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\TextInput::make('passing_percentage')
                                                    ->label('نسبة النجاح')
                                                    ->required()
                                                    ->numeric(),
                                                Forms\Components\TextInput::make('students_per_group')
                                                    ->label('عدد الطلاب في الحلقة')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\TextInput::make('session_duration')
                                                    ->label('مدة الجلسة بالدقائق')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\TextInput::make('max_pages_per_session')
                                                    ->label('أقصى عدد للصفحات في الجلسة')
                                                    ->required()
                                                    ->numeric(),



                                            ])->columns(2),

                                        Forms\Components\Section::make('إعدادات نوع القراءة')
                                            ->description('يمكنك إضافة عدة قراءات لاختيارها في الحصص')
                                            ->schema([
                                                Forms\Components\Repeater::make('reading_types')
                                                    ->label('أنواع القراءات')
                                                    ->simple(
                                                        Forms\Components\TextInput::make('reading_types')
                                                            ->label('اسم القراءة')
                                                            ->placeholder('مثال: حفص عن عاصم'),
                                                    )
                                                    ->minItems(1)
                                                    ->addable()
                                                    ->reorderable()
                                                    ->deletable()
                                            ]),

                                        Forms\Components\Section::make('إعدادات الإجازات')
                                            ->description('يمكنك إضافة عدة أنواع من الإجازات')
                                            ->schema([
                                                Forms\Components\Repeater::make('ijaza_types')
                                                    ->label('أنواع الإجازات')
                                                    ->simple(
                                                        Forms\Components\TextInput::make('ijaza_types')
                                                            ->label('اسم الإجازة')
                                                            ->placeholder('مثال: إجازة برواية حفص')
                                                            ->required(),
                                                    )
                                                    ->minItems(1)
                                                    ->addable()
                                                    ->reorderable()
                                                    ->deletable(),
                                            ]),
                                        Forms\Components\Section::make('المؤهلات العلمية')
                                            ->description('أضف المؤهلات العلمية التي يمكن تعيينها للطلاب')
                                            ->schema([
                                                Forms\Components\Repeater::make('qualifications')
                                                    ->label('المؤهلات')
                                                    ->simple(
                                                        Forms\Components\TextInput::make('qualifications')
                                                            ->label('اسم المؤهل')
                                                            ->placeholder('مثال: بكالوريوس')
                                                            ->required(),
                                                    )
                                                    ->minItems(1)
                                                    ->addable()
                                                    ->reorderable()
                                                    ->deletable(),
                                            ]),


                                        Forms\Components\Section::make('إعدادات التسجيل')
                                            ->description('تحديد طريقة قبول الطلاب الجدد')
                                            ->schema([
                                                Forms\Components\Toggle::make('auto_accept_students')
                                                    ->label('قبول الطلاب تلقائياً'),
                                            ]),
                                    ]),
                                Tabs\Tab::make('اعدادات البرامج')
                                    ->icon('icon-recitations')
                                    ->schema([
                                        Forms\Components\Section::make('برنامج المقرأة')
                                            ->description('اعدادات برنامج المقرأة')
                                            ->schema([
                                                Forms\Components\TextInput::make('maqraa_monthly_target')
                                                    ->label('عدد الاوجه الشهري')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\TextInput::make('maqraa_target_percentage')
                                                    ->label('نسبة النجاح')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\DatePicker::make('maqraa_start_date')
                                                    ->label('تاريخ البداية')
                                                    ->required(),

                                                Forms\Components\DatePicker::make('maqraa_end_date')
                                                    ->label('تاريخ النهاية')
                                                    ->required(),
                                                Forms\Components\Section::make()
                                                ->schema([
                                                    Forms\Components\TextInput::make('maqraa_sessions_per_month')
                                                    ->numeric()
                                                    ->label('عدد الحصص الشهرية')
                                                    ->required(),
                                                    Forms\Components\Repeater::make('maqraa_except_months_sessions')
                                                    ->label('الحصص المستثناة من الشهور')
                                                    ->schema([
                                                        
                                                        Forms\Components\Select::make('month')
                                                            ->label('الشهر')
                                                            ->options([
                                                                1 => 'يناير',
                                                                2 => 'فبراير',
                                                                3 => 'مارس',
                                                                4 => 'أبريل',
                                                                5 => 'مايو',
                                                                6 => 'يونيو',
                                                                7 => 'يوليو',
                                                                8 => 'أغسطس',
                                                                9 => 'سبتمبر',
                                                                10 => 'أكتوبر',
                                                                11 => 'نوفمبر',
                                                                12 => 'ديسمبر'
                                                            ])
                                                            ->required(),
                                                        Forms\Components\TextInput::make('count')->label('العدد')->required()->numeric(),
                                                    ])
                                                    ->columnSpanFull()
                                                    ->columns(2),
                                                ]),

                                                Forms\Components\CheckboxList::make('maqraa_study_days')
                                                    ->label('أيام العمل')
                                                    ->columnSpanFull()
                                                    ->columns(7)
                                                    ->options([
                                                        'saturday' => 'السبت',
                                                        'sunday' => 'الأحد',
                                                        'monday' => 'الإثنين',
                                                        'tuesday' => 'الثلاثاء',
                                                        'wednesday' => 'الأربعاء',
                                                        'thursday' => 'الخميس',
                                                        'friday' => 'الجمعة'
                                                    ]),

                                            ])
                                            ->columns(),

                                        Forms\Components\Section::make('برنامج التأسيس')
                                            ->description('اعدادات برنامج التأسيس')
                                            ->schema([
                                                Forms\Components\TextInput::make('mahir_monthly_target')
                                                    ->label('عدد الاوجه الشهري')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\TextInput::make('mahir_target_percentage')
                                                    ->label('نسبة النجاح')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\DatePicker::make('mahir_start_date')
                                                    ->label('تاريخ البداية')
                                                    ->required(),

                                                Forms\Components\DatePicker::make('mahir_end_date')
                                                    ->label('تاريخ النهاية')
                                                    ->required(),
                                                
                                                Forms\Components\Section::make()
                                                ->schema([
                                                    Forms\Components\TextInput::make('mahir_sessions_per_month')
                                                    ->numeric()
                                                    ->label('عدد الحصص الشهرية')
                                                    ->required(),
                                                    Forms\Components\Repeater::make('mahir_except_months_sessions')
                                                    ->label('الحصص المستثناة من الشهور')
                                                    ->schema([
                                                        
                                                        Forms\Components\Select::make('month')
                                                            ->label('الشهر')
                                                            ->options([
                                                                1 => 'يناير',
                                                                2 => 'فبراير',
                                                                3 => 'مارس',
                                                                4 => 'أبريل',
                                                                5 => 'مايو',
                                                                6 => 'يونيو',
                                                                7 => 'يوليو',
                                                                8 => 'أغسطس',
                                                                9 => 'سبتمبر',
                                                                10 => 'أكتوبر',
                                                                11 => 'نوفمبر',
                                                                12 => 'ديسمبر'
                                                            ])
                                                            ->required(),
                                                        Forms\Components\TextInput::make('count')->label('العدد')->required()->numeric(),
                                                    ])
                                                    ->columnSpanFull()
                                                    ->columns(2),
                                                ]),
                                                Forms\Components\CheckboxList::make('mahir_study_days')
                                                    ->label('أيام العمل')
                                                    ->columnSpanFull()
                                                    ->columns(7)
                                                    ->options([
                                                        'saturday' => 'السبت',
                                                        'sunday' => 'الأحد',
                                                        'monday' => 'الإثنين',
                                                        'tuesday' => 'الثلاثاء',
                                                        'wednesday' => 'الأربعاء',
                                                        'thursday' => 'الخميس',
                                                        'friday' => 'الجمعة'
                                                    ]),
                                            ])
                                            ->columns(),
                                        Forms\Components\Section::make('برنامج المتقن')
                                            ->description('اعدادات برنامج المتقن')
                                            ->schema([
                                                Forms\Components\TextInput::make('mutqin_mem_monthly_target')
                                                    ->label('عدد الاوجه الشهري - الحفظ')
                                                    ->required()
                                                    ->numeric(),



                                                Forms\Components\TextInput::make('mutqin_rev_monthly_target')
                                                    ->label('عدد الاوجه الشهري - المراجعة')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\TextInput::make('mutqin_target_percentage')
                                                    ->label('نسبة النجاح')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\DatePicker::make('mutqin_start_date')
                                                    ->label('تاريخ البداية')
                                                    ->required(),

                                                Forms\Components\DatePicker::make('mutqin_end_date')
                                                    ->label('تاريخ النهاية')
                                                    ->required(),
                                                Forms\Components\Section::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('mutqin_sessions_per_month')
                                                        ->numeric()
                                                        ->label('عدد الحصص الشهرية')
                                                        ->required(),
                                                        Forms\Components\Repeater::make('mutqin_except_months_sessions')
                                                        ->label('الحصص المستثناة من الشهور')
                                                        ->schema([
                                                            
                                                            Forms\Components\Select::make('month')
                                                                ->label('الشهر')
                                                                ->options([
                                                                    1 => 'يناير',
                                                                    2 => 'فبراير',
                                                                    3 => 'مارس',
                                                                    4 => 'أبريل',
                                                                    5 => 'مايو',
                                                                    6 => 'يونيو',
                                                                    7 => 'يوليو',
                                                                    8 => 'أغسطس',
                                                                    9 => 'سبتمبر',
                                                                    10 => 'أكتوبر',
                                                                    11 => 'نوفمبر',
                                                                    12 => 'ديسمبر'
                                                                ])
                                                                ->required(),
                                                            Forms\Components\TextInput::make('count')->label('العدد')->required()->numeric(),
                                                        ])
                                                        ->columnSpanFull()
                                                        ->columns(2),
                                                ]),
                                                Forms\Components\CheckboxList::make('mutqin_study_days')
                                                    ->label('أيام العمل')
                                                    ->columnSpanFull()
                                                    ->columns(7)
                                                    ->options([
                                                        'saturday' => 'السبت',
                                                        'sunday' => 'الأحد',
                                                        'monday' => 'الإثنين',
                                                        'tuesday' => 'الثلاثاء',
                                                        'wednesday' => 'الأربعاء',
                                                        'thursday' => 'الخميس',
                                                        'friday' => 'الجمعة'
                                                    ]),
                                            ])
                                            ->columns(2),

                                    ]),
                                Tabs\Tab::make('اعدادات الموقع')
                                    ->icon('icon-landing_page')
                                    ->schema([
                                        Forms\Components\Section::make('إعدادات البريد الإلكتروني')
                                            ->description('إعدادات إرسال الإشعارات')
                                            ->schema([
                                                Forms\Components\Toggle::make('send_notifications')
                                                    ->label('تفعيل الإشعارات')
                                                    ->required(),

                                                Forms\Components\TextInput::make('admin_email')
                                                    ->label('البريد الإلكتروني للمشرف')
                                                    ->required()
                                                    ->email(),
                                            ]),
                                        Forms\Components\Section::make('الإعدادات العامة')
                                            ->description('إعدادات عامة مثل اللغة والوضع الداكن')
                                            ->schema([
                                                Forms\Components\Select::make('default_language')
                                                    ->label('اللغة الافتراضية')
                                                    ->options([
                                                        'ar' => 'العربية',
                                                        'en' => 'الإنجليزية',
                                                    ])
                                                    ->required(),

                                                Forms\Components\Toggle::make('dark_mode')
                                                    ->label('تفعيل الوضع الليلي'),
                                                Forms\Components\Section::make('الشعار والهوية البصرية')
                                                        ->description('تحميل شعار وأيقونة الموقع')
                                                        ->schema([
                                                         Forms\Components\FileUpload::make('home_logo')
                                                        ->label('شعار المنشأة')
                                                        ->image(),
                                                         Forms\Components\FileUpload::make('logo')
                                                        ->label('شعار المقرأة')
                                                        ->image(),
                    
                                                Forms\Components\FileUpload::make('favicon')
                                                        ->label('الأيقونة')
                                                        ->image(),
                                                Forms\Components\TextInput::make('slider_title')
                                                        ->label('عنوان السلايدر للموقع الرئيسي'),
                                                Forms\Components\TextInput::make('student_login_button_title')
                                                        ->label('عنوان زر تسجيل دخول كطالب'),
                                                Forms\Components\TextInput::make('teacher_login_button_title')
                                                        ->label('عنوان زر تسجيل دخول كمعلم'),
                                                Forms\Components\TextInput::make('maqraa_slider_title')
                                                        ->label('عنوان السلايدر للمقرأة'),
                                                Forms\Components\Section::make('جزء الميزات')
                                                ->collapsible(true)
                                                ->collapsed(true)
                                                ->schema([
                                                    Forms\Components\Repeater::make('features_section')
                                                        ->label('جزء الميزات')
                                                        ->schema([
                                                            Forms\Components\TextInput::make('title')
                                                                ->label('الخاصية'),

                                                            Forms\Components\Textarea::make('value')
                                                                ->label('المحتوى'),
                                                        ])
                                                        ->reorderable(false)
                                                        ->deletable(false)
                                                        ->columns()
                                                ]),
                                                Forms\Components\Section::make('جزء التعريف')
                                                ->collapsible(true)
                                                ->collapsed(true)
                                                ->schema([
                                                    Forms\Components\Repeater::make('welcome_section')
                                                        ->label('جزء التعريف')
                                                        ->schema([
                                                            Forms\Components\TextInput::make('title')
                                                                ->label('الخاصية'),

                                                            Forms\Components\Textarea::make('value')
                                                                ->label('المحتوى'),
                                                        ])
                                                        ->reorderable(false)
                                                        ->deletable(false)
                                                        ->columns()
                                                ]),
                                                Forms\Components\Section::make('جزء خدماتنا')
                                                ->collapsible(true)
                                                ->collapsed(true)
                                                ->schema([
                                                    Forms\Components\Repeater::make('services_section')
                                                        ->label('جزء خدماتنا')
                                                        ->schema([
                                                            Forms\Components\TextInput::make('title')
                                                                ->label('الخاصية'),

                                                            Forms\Components\Textarea::make('value')
                                                                ->label('المحتوى'),
                                                        ])
                                                        ->reorderable(false)
                                                        ->deletable(false)
                                                        ->columns()
                                                    ])
                                                ])
                                            ]),
                                    ]),
                                Tabs\Tab::make('اعدادات التقييم والتقارير')
                                    // ->icon('icon-landing_page')
                                    ->schema([
                                        Forms\Components\Section::make('إعدادات التقارير')
                                            ->description('إعدادات التقارير الدورية')
                                            ->schema([
                                                Forms\Components\Toggle::make('enable_weekly_reports')
                                                    ->label('تفعيل التقارير الأسبوعية'),

                                                Forms\Components\Toggle::make('enable_monthly_reports')
                                                    ->label('تفعيل التقارير الشهرية'),

                                                Forms\Components\Toggle::make('enable_yearly_reports')
                                                    ->label('تفعيل التقارير السنوية'),
                                            ]),

                                        Forms\Components\Section::make('إعدادات التقييم')
                                            ->description('إعداد أوزان التقييم')
                                            ->schema([
                                                Forms\Components\TextInput::make('tajweed_weight')
                                                    ->label('وزن التجويد')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\TextInput::make('memorization_weight')
                                                    ->label('وزن الحفظ')
                                                    ->required()
                                                    ->numeric(),

                                                Forms\Components\TextInput::make('voice_quality_weight')
                                                    ->label('وزن جودة الصوت')
                                                    ->required()
                                                    ->numeric(),
                                            ]),

                                        Forms\Components\Section::make('إعدادات الأوسمة')
                                            ->description('تفعيل نظام الأوسمة والمكافآت')
                                            ->schema([
                                                Forms\Components\Toggle::make('badges_enabled')
                                                    ->label('تفعيل الأوسمة'),

                                                Forms\Components\Repeater::make('badges_levels')
                                                    ->label('مستويات الأوسمة')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('level')
                                                            ->label('اسم المستوى')
                                                            ->required(),

                                                        Forms\Components\TextInput::make('required_points')
                                                            ->label('النقاط المطلوبة')
                                                            ->required()
                                                            ->numeric(),
                                                    ])->columns(),

                                                Forms\Components\Repeater::make('badge_criteria')
                                                    ->label('معايير الحصول على الأوسمة')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('criteria')
                                                            ->label('المعيار')
                                                            ->required(),

                                                        Forms\Components\TextInput::make('points')
                                                            ->label('النقاط')
                                                            ->required()
                                                            ->numeric(),
                                                    ])->columns(),
                                            ]),
                                    ]),


                                Tabs\Tab::make('إعدادات المؤسسة')
                                    ->icon('icon-mosque')
                                    ->schema([
                                        Forms\Components\Section::make('معلومات المنشأة')
                                            ->description('إعدادات بيانات المؤسسة الأساسية')
                                            ->schema([
                                                Forms\Components\TextInput::make('company_name')
                                                    ->label('اسم المنشأة')
                                                    ->required(),
                                                Forms\Components\Textarea::make('company_description')
                                                    ->label('وصف المنشأة')
                                                    ->required(),

                                                Forms\Components\TextInput::make('branch_name')
                                                    ->label('اسم الفرع')
                                                    ->required(),
                                                Forms\Components\Textarea::make('branch_description')
                                                    ->label('وصف الفرع')
                                                    ->required(),
                                                Forms\Components\TextInput::make('company_manager')
                                                    ->label('مدير المنشأة')
                                                    ->required(),

                                                Forms\Components\TextInput::make('branch_manager')
                                                    ->label('مدير الفرع')
                                                    ->required(),

                                                Forms\Components\TextInput::make('contact_email')
                                                    ->label('البريد الإلكتروني للتواصل')
                                                    ->email()
                                                    ->required(),

                                                Forms\Components\TextInput::make('phone_number')
                                                    ->label('رقم الهاتف')
                                                    ->tel()
                                                    ->required(),
                                            ]),
                                    ]),

                                // Tabs\Tab::make('إعدادات التصميم')
                                //     ->icon('icon-image')
                                //     ->schema([
                                //         Forms\Components\Section::make('الشعار والهوية البصرية')
                                //             ->description('تحميل شعار وأيقونة الموقع')
                                //             ->schema([
                                //                 Forms\Components\FileUpload::make('logo')
                                //                     ->label('الشعار')
                                //                     ->image(),

                                //                 Forms\Components\FileUpload::make('favicon')
                                //                     ->label('الأيقونة')
                                //                     ->image(),
                                //             ]),
                                //     ]),

                                Tabs\Tab::make('إعدادات التقويم وأيام العمل')
                                    ->icon('icon-calendar')
                                    ->schema([
                                        Forms\Components\Section::make('إعدادات التقويم')
                                            ->description('تحديد نوع التقويم المستخدم')
                                            ->schema([
                                                Forms\Components\Select::make('calendar_type')
                                                    ->label('نوع التقويم')
                                                    ->options([
                                                        'hijri' => 'هجري',
                                                        'gregorian' => 'ميلادي',
                                                    ]),
                                            ]),

                                        Forms\Components\Section::make('أيام العمل')
                                            ->description('تحديد الأيام التي يتم فيها عقد الحصص')
                                            ->schema([
                                                Forms\Components\CheckboxList::make('working_days')
                                                    ->label('أيام العمل')
                                                    ->options([
                                                        'saturday' => 'السبت',
                                                        'sunday' => 'الأحد',
                                                        'monday' => 'الإثنين',
                                                        'tuesday' => 'الثلاثاء',
                                                        'wednesday' => 'الأربعاء',
                                                        'thursday' => 'الخميس',
                                                    ]),
                                            ]),
                                    ]),

                                Tabs\Tab::make('إعدادات أوقات الصلاة')
                                    ->icon('icon-mosque')
                                    ->schema([
                                        Forms\Components\Section::make('أوقات الصلاة')
                                            ->description('ضبط أوقات الصلاة لاستخدامها في جدولة الحصص')
                                            ->schema([
                                                Forms\Components\Repeater::make('prayer_times')
                                                    ->label('أوقات الصلاة')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('prayer_name')
                                                            ->label('اسم الصلاة')->disabled(),

                                                        Forms\Components\TimePicker::make('prayer_time')
                                                            ->label('وقت الصلاة'),
                                                    ])
                                                    ->minItems(5)
                                                    ->maxItems(5)
                                                    ->addable(false)
                                                    ->reorderable(false)
                                                    ->deletable(false)
                                                  ->columns(),
                                            ]),
                                    ]),
                            ])
                    ])->columnSpanFull()

            ]);
    }
}
