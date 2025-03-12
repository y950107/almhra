<?php

namespace App\Filament\Resources\SettingsResource\Pages;


use Tabs\Tab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\GeneralSettings;
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
        return $form
            ->schema([
                Forms\Components\Section::make('إعدادات الفئة العمرية')
                    ->description('تحديد الحد الأدنى والأقصى لعمر الطلاب')
                    ->schema([
                        Forms\Components\TextInput::make('min_age')
                            ->label('العمر الأدنى')
                            ->required()
                            ->numeric(),

                        Forms\Components\TextInput::make('max_age')
                            ->label('العمر الأقصى')
                            ->required()
                            ->numeric(),
                    ]),

                Forms\Components\Section::make('إعدادات النجاح')
                    ->description('تحديد النسبة المطلوبة للنجاح')
                    ->schema([
                        Forms\Components\TextInput::make('passing_percentage')
                            ->label('نسبة النجاح')
                            ->required()
                            ->numeric(),
                    ]),

                Forms\Components\Section::make('إعدادات المجموعات')
                    ->description('تحديد عدد الطلاب في الحلقة')
                    ->schema([
                        Forms\Components\TextInput::make('students_per_group')
                            ->label('عدد الطلاب في الحلقة')
                            ->required()
                            ->numeric(),
                    ]),
                    Forms\Components\Section::make('إعدادات نوع القراءة')
                    ->description('يمكنك إضافة عدة قراءات لاختيارها في الحصص')
                    ->schema([
                        Forms\Components\Repeater::make('reading_types')
                            ->label('أنواع القراءات')
                            ->schema([
                                Forms\Components\TextInput::make('reading')
                                    ->label('اسم القراءة')
                                    ->placeholder('مثال: حفص عن عاصم')
                                    ->required(),
                            ])
                            ->minItems(1)
                            ->addable()
                            ->reorderable()
                            ->deletable(),
                    ]),

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
                            ]),

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
                            ]),
                    ]),

                Forms\Components\Section::make('إعدادات التسجيل')
                    ->description('تحديد طريقة قبول الطلاب الجدد')
                    ->schema([
                        Forms\Components\Toggle::make('auto_accept_students')
                            ->label('قبول الطلاب تلقائياً'),
                    ]),

                Forms\Components\Section::make('إعدادات الجلسات')
                    ->description('إعدادات الجلسات وحجمها')
                    ->schema([
                        Forms\Components\TextInput::make('session_duration')
                            ->label('مدة الجلسة بالدقائق')
                            ->required()
                            ->numeric(),

                        Forms\Components\TextInput::make('max_pages_per_session')
                            ->label('أقصى عدد للصفحات في الجلسة')
                            ->required()
                            ->numeric(),
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
            ]);
    }
}
