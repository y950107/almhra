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

                                                Forms\Components\TextInput::make('branch_name')
                                                    ->label('اسم الفرع')
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
