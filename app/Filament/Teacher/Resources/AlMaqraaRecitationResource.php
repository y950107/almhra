<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\AlMaqraaRecitationResource\Pages;
use App\Filament\Teacher\Resources\AlMaqraaRecitationResource\RelationManagers;
use App\Models\AlMaqraaRecitation;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Filament\Forms\Get;
class AlMaqraaRecitationResource extends \App\Filament\Resources\AlMaqraaRecitationResource
{


    protected static ?string $model = AlMaqraaRecitation::class;

    protected static ?string $navigationIcon = 'icon-recitations';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->teacher->program_type === 'maqraa';
    }



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
                                                    
                                                    return $query->where('teacher_id', auth()->user()?->teacher?->id)
                                                        // ->where(function ($q) use ($maxStudents) {
                                                        //     $q->whereDoesntHave('students')
                                                        //     ->orWhereHas('students', function ($q) use ($maxStudents) {
                                                        //         $q->select('halaka_id')
                                                        //             ->groupBy('halaka_id')
                                                        //             ->havingRaw('count(*) < ?', [$maxStudents]);
                                                        //     });
                                                        // })
                                                        ;
                                                }
                                        )
                                        ->label('الحلقة')
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->native(false)
                                        ->disabledOn('edit')
                                        ->helperText(function ($get, $state) {
                                            // if (!$state) return 'اختر حلقة';

                                            // $halaka = Halaka::find($state);
                                            // if (!$halaka) return 'الحلقة غير موجودة';

                                            // $current = $halaka->students()->distinct('students.id')->count('students.id');

                                            // $max = app(GeneralSettings::class)->students_per_group;

                                            // return "الطلاب المسجلون: {$current}/{$max}";
                                            if (!$state) return 'اختر حلقة';

                                                $halaka = Halaka::find($state);
                                                if (!$halaka) return 'الحلقة غير موجودة';
                                                $current =$halaka->students_count;
                                                // $current = $halaka->students()->distinct('students.id')->count('students.id');
                                                // $max = app(GeneralSettings::class)->students_per_group;
    
                                                return $current;
                                        })
                                        ->reactive(),
                                    DatePicker::make('session_date')
                                        ->label('تاريخ الجلسة')
                                        ->default(today())
                                        ->required(),

                                        Select::make('student_id')
                                        ->relationship(
                                            name: 'student',
                                            titleAttribute: 'full_name', // Show names instead of IDs
                                            modifyQueryUsing: function (Builder $query, Get $get) {
                                                $halakaId = $get('halaka_id');
                                                return $query->whereHas('halakas.currentStudents', function ($q) use ($halakaId) {
                                                    $q->where('halakas.id', $halakaId);
                                                });
                                                // return $query
                                                //     ->where('teacher_id', auth()->user()?->teacher?->id)
                                                //     ->whereHas('candidate', fn($q) => $q->where('program_type', 'maqraa'))
                                                //     ->when($get('halaka_id'), function($q) use ($get) {
                                                //         // Check if halaka has sessions
                                                //         $hasSessions = RecitationSession::where('halaka_id', $get('halaka_id'))->exists();
                                                        
                                                //         if ($hasSessions) {
                                                //             // Show only students with sessions in this halaka
                                                //             return $q->whereHas('recitationSessions', fn($q) => 
                                                //                 $q->where('halaka_id', $get('halaka_id'))
                                                //             );
                                                //         } else {
                                                //             // Show teacher's mahir students without sessions
                                                //             return $q->whereDoesntHave('recitationSessions');
                                                //         }
                                                //     });
                                            }
                                        )
                                        ->preload()
                                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record?->candidate?->full_name}")
                                        ->label('الطالب')
                                        ->afterStateHydrated(function(Forms\Get $get, Forms\Set $set, ?RecitationSession $record) use ($quranService) {

                                            $student_id = $get('student_id');
                                            $almaqraa =  $record ? AlMaqraaRecitation::where('recitation_session_id', $record->id)->first() : null;
                                            if ($student_id && $almaqraa && $almaqraa->start_surah_id == null && $almaqraa->start_ayah_id == null) {
                                                $last_recitation = AlMaqraaRecitation::query()
                                                    ->where('end_ayah_id','!=',null)
                                                    ->whereHas('recitationSession.student', function ($query) use($student_id) {
                                                    $query->where('id', $student_id);
                                                })->whereHas('recitationSession', function ($query) {
                                                    $query->where('present', 'present');
                                                })->latest()->first();


                                                if ($last_recitation) {
                                                    // set last time end surah as this time start
                                                    $set('../start_surah_id', $last_recitation->end_surah_id);
                                                    $set('../start_ayah_id', $last_recitation->end_ayah_id);


                                                    if ($last_recitation->end_surah_id && $last_recitation->end_ayah_id)  {
                                                        $startPage = $quranService->getStartPageByAyah($last_recitation->end_surah_id, $last_recitation->end_ayah_id);
                                                        $set('../start_page', $startPage);
                                                    }
                                                }
                                            }
                                        })
                                        ->afterStateUpdated(function(Forms\Get $get, Forms\Set $set) use ($quranService) {

                                            $student_id = $get('student_id');
                                            $last_recitation = AlMaqraaRecitation::query()->whereHas('recitationSession.student', function ($query) use($student_id) {
                                                $query->where('id', $student_id);
                                            })->whereHas('recitationSession', function ($query) {
                                                $query->where('present', 'present');
                                            })->latest()->first();


                                            if ($last_recitation) {
                                                // set last time end surah as this time start
                                                $set('../start_surah_id', $last_recitation->end_surah_id);
                                                $set('../start_ayah_id', $last_recitation->end_ayah_id);


                                                if ($last_recitation->end_surah_id && $last_recitation->end_ayah_id)  {
                                                    $startPage = $quranService->getStartPageByAyah($last_recitation->end_surah_id, $last_recitation->end_ayah_id);
                                                    $set('../start_page', $startPage);
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
                                        ->options(RecitationSession::getPresentOptions())->live()
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
                                        )
                                        ->required(fn (Forms\Get $get): bool => $get('present') === 'present')
                                        ->visible(fn (Forms\Get $get): bool => $get('present') === 'present'),
                                ])->relationship('recitationSession')->columns()->statePath('recitationSession')->dehydrated(),

                        ]),

                        //  الأهداف القرآنية
                        Tabs\Tab::make('نتائج الحصة')->id("actual-results")
                            ->visible(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                            ->schema([
                                Select::make('start_surah_id')
                                    ->label('سورة البداية (اخر حصة)')
                                    ->options(fn() => collect($quranService->getSurahs())->pluck('name', 'id'))
                                    ->reactive()
                                    ->live()
                                    ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                    ->afterStateUpdated(fn($get, $set) => $set('start_ayah_id', null)),

                                Select::make('start_ayah_id')
                                    ->label('آية البداية (اخر حصة)')
                                    ->options(
                                        fn($get) => $get('start_surah_id')
                                            ? collect($quranService->getAyahs($get('start_surah_id')))
                                                ->mapWithKeys(function ($ayah) {
                                                    return [
                                                        $ayah['number'] => "{$ayah['number']} - "  . Str::limit($ayah['text'], 120)
                                                    ];
                                                })
                                            : []
                                    )
                                    ->reactive()
                                    ->live()
                                    ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                    ->afterStateHydrated(function ($get, $set) use ($quranService) {

                                        if ($get('start_surah_id') && $get('start_ayah_id')) {
                                            $startPage = $quranService->getStartPageByAyah($get('start_surah_id'), $get('start_ayah_id'));
                                            $set('start_page', $startPage);
                                        }
                                    })
                                    ->afterStateUpdated(function ($get, $set) use ($quranService) {

                                        if ($get('start_surah_id') && $get('start_ayah_id')) {
                                            $startPage = $quranService->getStartPageByAyah($get('start_surah_id'), $get('start_ayah_id'));
                                            $set('start_page', $startPage);
                                        }


                                        if ($get('end_ayah_id')) {
                                            // حساب عدد الأسطر
                                            $targetLines = $quranService->calculateLines(
                                                $get('start_surah_id'),
                                                $get('start_ayah_id'),
                                                $get('end_surah_id'),
                                                $get('end_ayah_id')
                                            );

                                            // حساب عدد الصفحات
                                            $startSurahId = $get('start_surah_id');


                                            if ($startSurahId == 1) {
                                                $targetLines -= 7;
                                                $targetPages = round($targetLines / 15,1) + 1;
                                            } else {
                                                $targetPages = round($targetLines / 15,1);
                                            }

                                            $set('pages', $targetPages);
                                        }

                                    }),


                                Select::make('end_surah_id')
                                    ->label('سورة النهاية')
                                    ->options(fn() => collect($quranService->getSurahs())->pluck('name', 'id'))
                                    ->reactive()
                                    ->live()
                                    ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                    ->afterStateUpdated(fn($get, $set) => $set('end_ayah_id', null)),

                                Select::make('end_ayah_id')
                                    ->label('آية النهاية')
                                    ->options(
                                        fn($get) => $get('end_surah_id')
                                            ? collect($quranService->getAyahs($get('end_surah_id')))
                                                ->mapWithKeys(function ($ayah) {
                                                    return [
                                                        $ayah['number'] => "{$ayah['number']} - "  . Str::limit($ayah['text'], 120)
                                                    ];
                                                })
                                            : []
                                    )
                                    ->reactive()
                                    ->live()
                                    ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                    ->afterStateHydrated(function ($get, $set,string $operation) use ($quranService) {
                                        // حساب عدد الأسطر
                                        if ($operation === 'edit') {
                                            if ($get('end_surah_id') && $get('end_ayah_id')) {
                                                $startPage = $quranService->getStartPageByAyah($get('end_surah_id'), $get('end_ayah_id'));
                                                $set('end_page', $startPage);
                                            }
                                        }

                                    })
                                    ->afterStateUpdated(function ($get, $set) use ($quranService) {

                                        if ($get('end_surah_id') && $get('end_ayah_id')) {
                                            $startPage = $quranService->getStartPageByAyah($get('end_surah_id'), $get('end_ayah_id'));
                                            $set('end_page', $startPage);
                                        };

                                        if ($get('start_ayah_id')) {
                                            // حساب عدد الأسطر
                                            $targetLines = $quranService->calculateLines(
                                                $get('start_surah_id'),
                                                $get('start_ayah_id'),
                                                $get('end_surah_id'),
                                                $get('end_ayah_id')
                                            );



                                            // حساب عدد الصفحات
                                            $startSurahId = $get('start_surah_id');


                                            if ($startSurahId == 1) {
                                                $targetLines -= 7;
                                                $targetPages = round($targetLines / 15,1) + 1;
                                            } else {
                                                $targetPages = round($targetLines / 15,1);
                                            }

                                            $set('pages', $targetPages);
                                        }

                                    }),

                                TextInput::make('start_page')
                                    ->label('صفحة البداية')
                                    ->numeric()
                                    ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                    ->disabled(),

                                TextInput::make('end_page')
                                    ->label('صفحة النهاية')
                                    ->numeric()
                                    ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                    ->disabled(),


                                TextInput::make('pages')
                                    ->label('عدد الاوجه')
                                    ->numeric()
                                    ->disabled()
                                    ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                    ->dehydrated(),
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
                                        ->label('التجويد')
                                        ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present'),

                                    TextInput::make('fluency_score')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->suffix('من 100')
                                        ->label('')
                                        ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present'),

                                    TextInput::make('memory_score')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->suffix('من 100')
                                        ->label('الحفظ')
                                        ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present'),
                                ])
                                // Remove these lines as they're incorrectly placed here:
                                // ->relationship('recitationSession')
                                // ->statePath('recitationSession')
                                // ->dehydrated()


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

        return  $table
            ->query(
                AlMaqraaRecitation::query()->whereHas('recitationSession.halaka.teacher', function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            )->filters([
                // Filter by student
                SelectFilter::make('student_id')
                    ->label('الطلاب')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->relationship('recitationSession.student', 'id',modifyQueryUsing: function (Builder $query) {
                        $query->where('teacher_id',auth()->user()?->teacher?->id);
                    })->getOptionLabelFromRecordUsing(fn($record) => "{$record?->candidate?->full_name}"),

                // Filter by date from
                Filter::make('from_date')
                    ->label('من تاريخ')
                    ->form([
                        DatePicker::make('from')->label('من تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['from']) {
                            $query->whereHas('recitationSession', fn ($q) =>
                            $q->whereDate('session_date', '>=', $data['from'])
                            );
                        }
                    })    ->indicateUsing(function (array $data): ?string {
                        return $data['from'] ? 'من: ' . \Carbon\Carbon::parse($data['from'])->format('Y-m-d') : null;
                    }),

                // Filter by date to
                Filter::make('to_date')
                    ->label('إلى تاريخ')
                    ->form([
                        DatePicker::make('to')->label('إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['to']) {
                            $query->whereHas('recitationSession', fn ($q) =>
                            $q->whereDate('session_date', '<=', $data['to'])
                            );
                        }
                    })   ->indicateUsing(function (array $data): ?string {
                        return $data['to'] ? 'إلى: ' . \Carbon\Carbon::parse($data['to'])->format('Y-m-d') : null;
                    }),
            ])
            ->headerActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
