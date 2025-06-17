<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\AlMutqinRecitationResource\Pages;
use App\Filament\Teacher\Resources\AlMutqinRecitationResource\RelationManagers;
use App\Models\AlMutqinRecitation;
use App\Models\Halaka;
use App\Models\RecitationSession;
use App\Services\Moshaf_madina_Service;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AlMutqinRecitationResource extends \App\Filament\Resources\AlMutqinRecitationResource
{

    protected static ?string $model = AlMutqinRecitation::class;

    protected static ?string $navigationIcon = 'icon-recitations';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->teacher->program_type === 'mutqin';
    }

    protected static ?int $navigationSort = 4;



    public static function form(Form $form): Form
    {

        $quranService = new Moshaf_madina_Service();
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Tabs::make('تفاصيل الجلسة')->tabs([

                            //  المعلومات الأساسية
                            Tabs\Tab::make('المعلومات الأساسية')

                                ->schema([
                                    Forms\Components\Group::make([
                                        Select::make('halaka_id')
                                            ->relationship(
                                                name: 'halaka',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: function (Builder $query) {
                                                    $maxStudents = app(GeneralSettings::class)->students_per_group;

                                                    return $query->where('teacher_id',auth()->user()?->teacher?->id)
                                                        ->where(function ($q) use ($maxStudents) {
                                                            $q->whereHas('students', function ($q) use ($maxStudents) {
                                                                $q->groupBy('halaka_id')
                                                                    ->havingRaw('count(*) < ?', [$maxStudents]);
                                                            })->orWhereDoesntHave('students');
                                                        });
                                                }
                                            )
                                            ->label('الحلقة')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->disabledOn('edit')
                                            ->helperText(function ($get, $state) {
                                                if (!$state) return 'اختر حلقة';

                                                $halaka = Halaka::find($state);
                                                if (!$halaka) return 'الحلقة غير موجودة';

                                                $current = $halaka->students()->distinct('students.id')->count('students.id');

                                                $max = app(GeneralSettings::class)->students_per_group;

                                                return "الطلاب المسجلون: {$current}/{$max}";
                                            })
                                            ->reactive(),
                                        DatePicker::make('session_date')
                                            ->label('تاريخ الجلسة')
                                            ->default(today())
                                            ->required(),

                                        Select::make('student_id')
                                            ->relationship(
                                                name: 'student',
                                                titleAttribute: 'id',
                                                modifyQueryUsing: fn($query) => $query->whereHas('candidate', function ($q) {
                                                    $q->where('program_type', 'mutqin');
                                                }),
                                            )
                                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->candidate->full_name}")
                                            ->label('الطالب')
                                            ->afterStateHydrated(function(Forms\Get $get, Forms\Set $set, ?RecitationSession $record) use ($quranService) {

                                                $student_id = $get('student_id');
                                                $almutqin =  $record ? AlMutqinRecitation::where('recitation_session_id', $record->id)->first() : null;
                                                if ($student_id && $almutqin ) {
                                                    $last_recitation = AlMutqinRecitation::query()
                                                        ->where('mem_end_ayah_id','!=',null)
                                                        ->whereHas('recitationSession.student', function ($query) use($student_id) {
                                                            $query->where('id', $student_id);
                                                        })->whereHas('recitationSession', function ($query) {
                                                            $query->where('present', 'present');
                                                        })->latest()->first();


                                                    if ($last_recitation && $almutqin->mem_start_surah_id == null && $almutqin->mem_start_ayah_id == null) {
                                                        // set last time end surah as this time start
                                                        $set('../mem_start_surah_id', $last_recitation->mem_end_surah_id);
                                                        $set('../mem_start_ayah_id', $last_recitation->mem_end_ayah_id);


                                                        if ($last_recitation->mem_end_surah_id && $last_recitation->mem_end_ayah_id)  {
                                                            $startPage = $quranService->getStartPageByAyah($last_recitation->mem_end_surah_id, $last_recitation->mem_end_ayah_id);
                                                            $set('../mem_start_page', $startPage);
                                                        }
                                                    }

                                                    if ($last_recitation && $almutqin->rev_start_surah_id == null && $almutqin->rev_start_ayah_id == null) {
                                                        // set last time end surah as this time start
                                                        $set('../rev_start_surah_id', $last_recitation->rev_end_surah_id);
                                                        $set('../rev_start_ayah_id', $last_recitation->rev_end_ayah_id);


                                                        if ($last_recitation->rev_end_surah_id && $last_recitation->rev_end_ayah_id)  {
                                                            $startPage = $quranService->getStartPageByAyah($last_recitation->rev_end_surah_id, $last_recitation->rev_end_ayah_id);
                                                            $set('../rev_start_page', $startPage);
                                                        }
                                                    }
                                                }
                                            })
                                            ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set) use ($quranService) {

                                                $student_id = $get('student_id');
                                                $last_recitation = AlMutqinRecitation::query()->whereHas('recitationSession.student', function ($query) use($student_id) {
                                                    $query->where('id', $student_id);
                                                })->whereHas('recitationSession', function ($query) {
                                                    $query->where('present', 'present');
                                                })->latest()->first();


                                                if ($last_recitation) {
                                                    // set last time end surah as this time start
                                                    $set('../mem_start_surah_id', $last_recitation->mem_end_surah_id);
                                                    $set('../mem_start_ayah_id', $last_recitation->mem_end_ayah_id);

                                                    $set('../rev_start_surah_id', $last_recitation->rev_end_surah_id);
                                                    $set('../rev_start_ayah_id', $last_recitation->rev_end_ayah_id);


                                                    if ($last_recitation->mem_end_surah_id && $last_recitation->mem_end_ayah_id)  {
                                                        $startPage = $quranService->getStartPageByAyah($last_recitation->mem_end_surah_id, $last_recitation->mem_end_ayah_id);
                                                        $set('../mem_start_page', $startPage);
                                                    }

                                                    if ($last_recitation->rev_end_surah_id && $last_recitation->rev_end_ayah_id)  {
                                                        $startPage = $quranService->getStartPageByAyah($last_recitation->rev_end_surah_id, $last_recitation->rev_end_ayah_id);
                                                        $set('../rev_start_page', $startPage);
                                                    }
                                                }

                                            })
                                            ->disabledOn('edit')
                                            ->live()
                                            ->columnSpanFull()
                                            ->required(),


                                        Select::make('present')
                                            ->label('الحضور')
                                            ->columnSpanFull()
                                            ->options(RecitationSession::getPresentOptions())
                                            ->live()
                                            ->required(),

                                        Select::make('recitation_type')
                                            ->label('طريقة التسميع')
                                            ->options([
                                                'in_person' => 'حضوري',
                                                'remote' => 'عن بُعد',
                                            ])
                                            ->visible(fn (Forms\Get $get): bool => $get('present') === 'present')
                                            ->required(fn (Forms\Get $get): bool => $get('present') === 'present'),

                                        Select::make('recitation_narration')
                                            ->label('القراءة / الرواية')
                                            ->options(
                                                collect(settings('reading_types', []))
                                                    ->mapWithKeys(function ($value) {
                                                        return [$value => $value];
                                                    })
                                                    ->toArray()
                                            )                                            ->visible(fn (Forms\Get $get): bool => $get('present') === 'present'),
                                    ])->relationship('recitationSession')->columns()->statePath('recitationSession')->dehydrated(),

                                ]),

                            //  الأهداف القرآنية
                            Tabs\Tab::make('نتائج الحفظ')
                                ->visible(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                ->schema([
                                    Select::make('mem_start_surah_id')
                                        ->label('سورة البداية (اخر حصة)')
                                        ->options(fn() => collect($quranService->getSurahs())->pluck('name', 'id'))
                                        ->reactive()
                                        ->live()
                                        ->afterStateUpdated(fn($get, $set) => $set('mem_start_ayah_id', null)),

                                    Select::make('mem_start_ayah_id')
                                        ->label('آية البداية (اخر حصة)')
                                        ->options(
                                            fn($get) => $get('mem_start_surah_id')
                                                ? collect($quranService->getAyahs($get('mem_start_surah_id')))
                                                    ->mapWithKeys(function ($ayah) {
                                                        return [
                                                            $ayah['number'] => "{$ayah['number']} - "  . Str::limit($ayah['text'], 120)
                                                        ];
                                                    })
                                                : []
                                        )
                                        ->reactive()
                                        ->live()
                                        ->afterStateHydrated(function ($get, $set) use ($quranService) {

                                            if ($get('mem_start_surah_id') && $get('mem_start_ayah_id')) {
                                                $startPage = $quranService->getStartPageByAyah($get('mem_start_surah_id'), $get('mem_start_ayah_id'));
                                                $set('mem_start_page', $startPage);
                                            }
                                        })
                                        ->afterStateUpdated(function ($get, $set) use ($quranService) {

                                            if ($get('mem_start_surah_id') && $get('mem_start_ayah_id')) {
                                                $startPage = $quranService->getStartPageByAyah($get('mem_start_surah_id'), $get('mem_start_ayah_id'));
                                                $set('mem_start_page', $startPage);
                                            }
                                        }),


                                    Select::make('mem_end_surah_id')
                                        ->label('سورة النهاية')
                                        ->options(fn() => collect($quranService->getSurahs())->pluck('name', 'id'))
                                        ->reactive()
                                        ->live()
                                        ->afterStateUpdated(fn($get, $set) => $set('mem_end_ayah_id', null)),

                                    Select::make('mem_end_ayah_id')
                                        ->label('آية النهاية')
                                        ->options(
                                            fn($get) => $get('mem_end_surah_id')
                                                ? collect($quranService->getAyahs($get('mem_end_surah_id')))
                                                    ->mapWithKeys(function ($ayah) {
                                                        return [
                                                            $ayah['number'] => "{$ayah['number']} - "  . Str::limit($ayah['text'], 120)
                                                        ];
                                                    })
                                                : []
                                        )
                                        ->reactive()
                                        ->live()
                                        ->afterStateHydrated(function ($get, $set,string $operation) use ($quranService) {
                                            // حساب عدد الأسطر
                                            if ($operation === 'edit') {
                                                if ($get('mem_end_surah_id') && $get('mem_end_ayah_id')) {
                                                    $startPage = $quranService->getStartPageByAyah($get('mem_end_surah_id'), $get('mem_end_ayah_id'));
                                                    $set('mem_end_page', $startPage);
                                                }
                                            }

                                        })
                                        ->afterStateUpdated(function ($get, $set) use ($quranService) {
                                            // حساب عدد الأسطر
                                            $targetLines = $quranService->calculateLines(
                                                $get('mem_start_surah_id'),
                                                $get('mem_start_ayah_id'),
                                                $get('mem_end_surah_id'),
                                                $get('mem_end_ayah_id')
                                            );
                                            if ($get('mem_end_surah_id') && $get('mem_end_ayah_id')) {
                                                $startPage = $quranService->getStartPageByAyah($get('mem_end_surah_id'), $get('mem_end_ayah_id'));
                                                $set('mem_end_page', $startPage);
                                            };
                                            // حساب عدد الصفحات
                                            $startSurahId = $get('mem_start_surah_id');


                                            if ($startSurahId == 1) {
                                                $targetLines -= 7;
                                                $targetPages = ceil($targetLines / 15) + 1;
                                            } else {
                                                $targetPages = ceil($targetLines / 15);
                                            }
                                            $targetPages = number_format($targetPages, 2);
                                            $set('mem_pages', $targetPages);
                                        }),

                                    TextInput::make('mem_start_page')
                                        ->label('صفحة البداية')
                                        ->numeric()
                                        ->disabled(),

                                    TextInput::make('mem_end_page')
                                        ->label('صفحة النهاية')
                                        ->numeric()
                                        ->disabled(),


                                    TextInput::make('mem_pages')
                                        ->label('عدد الاوجه')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(),
                                ]),

                            Tabs\Tab::make('نتائج المراجعة')
                                ->visible(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                ->schema([
                                    Select::make('rev_start_surah_id')
                                        ->label('سورة البداية (اخر حصة)')
                                        ->options(fn() => collect($quranService->getSurahs())->pluck('name', 'id'))
                                        ->reactive()
                                        ->live()
                                        ->afterStateUpdated(fn($get, $set) => $set('rev_start_ayah_id', null)),

                                    Select::make('rev_start_ayah_id')
                                        ->label('آية البداية (اخر حصة)')
                                        ->options(
                                            fn($get) => $get('rev_start_surah_id')
                                                ? collect($quranService->getAyahs($get('rev_start_surah_id')))
                                                    ->mapWithKeys(function ($ayah) {
                                                        return [
                                                            $ayah['number'] => "{$ayah['number']} - "  . Str::limit($ayah['text'], 120)
                                                        ];
                                                    })
                                                : []
                                        )
                                        ->reactive()
                                        ->live()
                                        ->afterStateHydrated(function ($get, $set) use ($quranService) {

                                            if ($get('rev_start_surah_id') && $get('rev_start_ayah_id')) {
                                                $startPage = $quranService->getStartPageByAyah($get('rev_start_surah_id'), $get('rev_start_ayah_id'));
                                                $set('rev_start_page', $startPage);
                                            }
                                        })
                                        ->afterStateUpdated(function ($get, $set) use ($quranService) {

                                            if ($get('rev_start_surah_id') && $get('rev_start_ayah_id')) {
                                                $startPage = $quranService->getStartPageByAyah($get('rev_start_surah_id'), $get('rev_start_ayah_id'));
                                                $set('rev_start_page', $startPage);
                                            }
                                        }),


                                    Select::make('rev_end_surah_id')
                                        ->label('سورة النهاية')
                                        ->options(fn() => collect($quranService->getSurahs())->pluck('name', 'id'))
                                        ->reactive()
                                        ->live()
                                        ->afterStateUpdated(fn($get, $set) => $set('rev_end_ayah_id', null)),

                                    Select::make('rev_end_ayah_id')
                                        ->label('آية النهاية')
                                        ->options(
                                            fn($get) => $get('rev_end_surah_id')
                                                ? collect($quranService->getAyahs($get('rev_end_surah_id')))
                                                    ->mapWithKeys(function ($ayah) {
                                                        return [
                                                            $ayah['number'] => "{$ayah['number']} - " . Str::limit($ayah['text'], 120)
                                                        ];
                                                    })
                                                : []
                                        )
                                        ->reactive()
                                        ->live()
                                        ->afterStateHydrated(function ($get, $set,string $operation) use ($quranService) {
                                            // حساب عدد الأسطر
                                            if ($operation === 'edit') {
                                                if ($get('rev_end_surah_id') && $get('rev_end_ayah_id')) {
                                                    $startPage = $quranService->getStartPageByAyah($get('rev_end_surah_id'), $get('rev_end_ayah_id'));
                                                    $set('rev_end_page', $startPage);
                                                }
                                            }

                                        })
                                        ->afterStateUpdated(function ($get, $set) use ($quranService) {
                                            // حساب عدد الأسطر
                                            $targetLines = $quranService->calculateLines(
                                                $get('rev_start_surah_id'),
                                                $get('rev_start_ayah_id'),
                                                $get('rev_end_surah_id'),
                                                $get('rev_end_ayah_id')
                                            );
                                            if ($get('rev_end_surah_id') && $get('rev_end_ayah_id')) {
                                                $startPage = $quranService->getStartPageByAyah($get('rev_end_surah_id'), $get('rev_end_ayah_id'));
                                                $set('rev_end_page', $startPage);
                                            };
                                            // حساب عدد الصفحات
                                            $startSurahId = $get('rev_start_surah_id');


                                            if ($startSurahId == 1) {
                                                $targetLines -= 7;
                                                $targetPages = ceil($targetLines / 15) + 1;
                                            } else {
                                                $targetPages = ceil($targetLines / 15);
                                            }
                                            $targetPages = number_format($targetPages, 2);
                                            $set('rev_pages', $targetPages);
                                        }),

                                    TextInput::make('rev_start_page')
                                        ->label('صفحة البداية')
                                        ->numeric()
                                        ->disabled(),

                                    TextInput::make('rev_end_page')
                                        ->label('صفحة النهاية')
                                        ->numeric()
                                        ->disabled(),


                                    TextInput::make('rev_pages')
                                        ->label('عدد الاوجه')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(),
                                ]),

                            Tabs\Tab::make('الدرس المصاحب')
                                ->visible(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                ->schema([
                                    Select::make('lesson_type')
                                        ->label('الدرس المصاحب ')
                                        ->options(AlMutqinRecitation::getLessonTypes())
                                        ->columnSpanFull(),

                                    TextInput::make('lesson_title')
                                        ->label('عنوان الدرس المصاحب')
                                        ->required(fn($get) => $get('lesson_type') !== null)
                                        ->columnSpanFull(),

                                ]),


                            // تقييمات المعلم
                            Tabs\Tab::make('تقييمات المعلم')
                                ->visible(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                ->schema([

                                    Forms\Components\Group::make([
                                        TextInput::make('tajweed_score')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('من 100')
                                            ->label('درجة التجويد'),

                                        TextInput::make('fluency_score')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('من 100')
                                            ->label('درجة الطلاقة'),

                                        TextInput::make('memory_score')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('من 100')
                                            ->label('درجة الحفظ'),
                                    ])->relationship('recitationSession')->statePath('recitationSession')->dehydrated()


                                ]),
                            //  الملاحظات العامة
                            Tabs\Tab::make('الملاحظات العامة')->schema([
                                Textarea::make('evaluation_notes')->label('ملاحظات المعلم'),
                                Textarea::make('notes')->label('ملاحظات عامة'),
                            ])
                        ])->columnSpanFull()->persistTabInQueryString()
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {

        $table = parent::table($table);

        return $table
            ->query(
                AlMutqinRecitation::query()->whereHas('recitationSession.halaka.teacher.user', function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            )
            ->headerActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecitations::route('/'),
            'create' => Pages\CreateRecitation::route('/create'),
            'edit' => Pages\EditRecitation::route('/{record}/edit'),
        ];
    }
}
