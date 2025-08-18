<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\AlMaherRecitationResource\Pages;
use App\Filament\Teacher\Resources\AlMaherRecitationResource\RelationManagers;
use App\Models\AlMaherRecitation;
use App\Models\Halaka;
use App\Models\Student;
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
use Filament\Forms\Components\Grid;
class AlMaherRecitationResource extends \App\Filament\Resources\AlMaherRecitationResource
{

    protected static ?string $model = AlMaherRecitation::class;

    protected static ?string $navigationIcon = 'icon-recitations';

    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->teacher->program_type === 'mahir';
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
                                                    return $query->whereHas('halakas', function ($q) use ($halakaId) {
                                                        $q->where('halakas.id', $halakaId)
                                                          ->whereNull('halaka_student.moved_at'); // Pivot table condition
                                                    });
                                                    // return $query
                                                    //     ->where('teacher_id', auth()->user()?->teacher?->id)
                                                    //     ->whereHas('candidate', fn($q) => $q->where('program_type', 'mahir'))
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
                                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->candidate->full_name}")
                                            ->label('الطالب')
                                            ->afterStateHydrated(function(Forms\Get $get, Forms\Set $set, ?RecitationSession $record) use ($quranService) {

                                                $student_id = $get('student_id');
                                                $almaher =  $record ? AlMaherRecitation::where('recitation_session_id', $record->id)->first() : null;
                                                if ($student_id && $almaher && $almaher->start_surah_id == null && $almaher->start_ayah_id == null) {
                                                    $last_recitation = AlMaherRecitation::query()
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
                                                $last_recitation = AlMaherRecitation::query()->whereHas('recitationSession.student', function ($query) use($student_id) {
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

                            Tabs\Tab::make('المتن المصاحب')
                                ->visible(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                ->schema([
                                    Select::make('lesson_title')
                                        ->label('حفظ المتون')
                                        ->options(AlMaherRecitation::getLessonTitles())
                                        ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                        ->columnSpanFull(),

                                    TextInput::make('mem_lines')
                                        ->label('عدد الأبيات المحفوظة')
                                        ->required(fn($get) => $get('lesson_title') !== null)
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
                                            ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                            ->label('التجويد'),

                                        TextInput::make('fluency_score')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('من 100')
                                            ->label('الأداء')
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

        return $table
            ->query(
                AlMaherRecitation::query()->whereHas('recitationSession.halaka.teacher.user', function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            )->filters([
                SelectFilter::make('student_filter')
                ->label('تصفية الطلاب')
                ->form([
                    Grid::make(2)
                    ->schema([
                        // Halaka dropdown
                        Select::make('halaka_id')
                            ->label('الحلقة')
                            ->options(fn() => Halaka::query()
                                ->latest()
                                ->whereTeacherId(auth()->user()->teacher?->id)
                                ->pluck('name', 'id')
                                ->toArray()
                            )
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $halaka = Halaka::find($state);
                                if ($halaka) {
                                    $studentId = (int) $get('student_id');
                                    if ($studentId && $student = Student::find($studentId)) {
                                        if (!$student->halakas()->where('halakas.id', $halaka->id)->exists()) {
                                            $set('student_id', null);
                                        }
                                    }
                                }
                            })
                            ->reactive()
                            ->searchable(),
                            
                        // Student dropdown (dependent on halaka selection)
                        Select::make('student_id')
                            ->label('الطالب')
                            ->options(function (callable $get) {
                                $halakaId = $get('halaka_id');
                                if ($halakaId) {
                                    return Student::query()
                                        ->whereHas('halakas', function($q) use ($halakaId) {
                                            $q->where('halakas.id', $halakaId)
                                              ->whereNull('halaka_student.moved_at'); // Pivot table condition
                                        })
                                        ->whereHas('candidate', fn($q) => $q->where('status', 'accepted')->where('program_type', 'mahir'))
                                        ->with('candidate')
                                        ->get()
                                        ->mapWithKeys(fn($student) => [$student->id => $student->candidate->full_name])
                                        ->toArray();
                                }
                                return Student::query()
                                    ->whereHas('candidate', fn($q) => $q->where('status', 'accepted')->where('program_type', 'mahir'))
                                    ->with('candidate')
                                    ->get()
                                    ->mapWithKeys(fn($student) => [$student->id => $student->candidate->full_name])
                                    ->toArray();
                            })
                            ->searchable()
                        ])
                    
                ])
                ->query(function (Builder $query, array $data) {
                    $studentId = (int) $data['student_id'] ?? null;
                    $halakaId = (int) $data['halaka_id'] ?? null;
                    
                    if ($studentId) {
                        $query->whereHas('recitationSession', fn($q) => $q->where('student_id', $studentId));
                    }
                    
                    if ($halakaId) {
                        $query->whereHas('recitationSession', fn($q) => $q->where('halaka_id', $halakaId));
                    }
                })
                ->indicateUsing(function (array $state): ?string {
                    $indicators = [];
                    
                    if (!empty($state['halaka_id'])) {
                        $halaka = Halaka::find($state['halaka_id']);
                        if ($halaka) {
                            $indicators[] = 'الحلقة: ' . $halaka->name;
                        }
                    }
                    
                    if (!empty($state['student_id'])) {
                        $student = Student::with('candidate')->find($state['student_id']);
                        if ($student) {
                            $indicators[] = 'الطالب: ' . $student->candidate->full_name;
                        }
                    }
                    
                    return $indicators ? implode(' - ', $indicators) : null;
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


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecitations::route('/'),
            'create' => Pages\CreateRecitation::route('/create'),
            'edit' => Pages\EditRecitation::route('/{record}/edit'),
        ];
    }
}
