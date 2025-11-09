<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Halaka;
use App\Models\Teacher;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Models\Student;
use App\Models\RecitationSession;
use App\Settings\GeneralSettings;
use App\Models\AlMaqraaRecitation;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use App\Services\Moshaf_madina_Service;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Grid;
use App\Filament\Resources\AlMaqraaRecitationResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\AlMaqraaRecitationResource\RelationManagers;

class AlMaqraaRecitationResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    protected static ?string $slug = "al-maqraa";

    protected static ?string $model = AlMaqraaRecitation::class;

    protected static ?string $navigationIcon = 'icon-recitations';

    protected static ?int $navigationSort = 3;


    public static function getNavigationLabel(): string
    {
        return __('filament.almaqraa-recitation.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.almaqraa-recitation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.almaqraa-recitation.navigation_label');

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

                                                return $query
                                                    // ->where(function ($q) use ($maxStudents) {
                                                    //     $q->whereHas('students', function ($q) use ($maxStudents) {
                                                    //         $q->groupBy('halaka_id')
                                                    //             ->havingRaw('count(*) < ?', [$maxStudents]);
                                                    //     })->orWhereDoesntHave('students');
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
                                            // dd($halaka->students);
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
                                            titleAttribute: 'full_name',
                                            modifyQueryUsing: function (Builder $query, Get $get) {
                                                $halakaId = $get('halaka_id');
                                                //return halaka currentStudents
                                               return $query->whereHas('halakas', function ($q) use ($halakaId) {
                                                    $q->where('halakas.id', $halakaId)
                                                      ->whereNull('halaka_student.moved_at'); // Pivot table condition
                                                });
                                                // return $query->when($halakaId, function ($q) use ($halakaId) {
                                                //     // Check if halaka has any sessions
                                                //     $hasSessions = DB::table('recitation_sessions')
                                                //         ->where('halaka_id', $halakaId)
                                                //         ->exists();
                                                    
                                                //     if ($hasSessions) {
                                                //         // Case 1: Halaka has sessions - get only students with sessions
                                                //         return $q->whereHas('recitationSessions', fn($q) => 
                                                //             $q->where('halaka_id', $halakaId)
                                                //         );
                                                //     } else {
                                                //         // Case 2: Halaka has no sessions - get all teacher's students
                                                //         $teacherId = Halaka::find($halakaId)?->teacher_id;
                                                //         return $q->where('teacher_id', $teacherId)
                                                //             ->whereDoesntHave('recitationSessions');
                                                //     }
                                                // });
                                            }
                                        )
                                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->candidate->full_name}")
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

                                })
                                ->visible(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present'),


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

                                })
                                ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present'),

                            TextInput::make('start_page')
                                ->label('صفحة البداية')
                                ->numeric()
                                ->disabled()
                                ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present'),

                            TextInput::make('end_page')
                                ->label('صفحة النهاية')
                                ->numeric()
                                ->disabled()
                                ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present'),


                            TextInput::make('pages')
                                ->label('عدد الاوجه')
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->required(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present'),
                        ]),



                        Tabs\Tab::make('تقييمات المعلم')
                        ->visible(fn (Get $get): bool => $get('recitationSession.present') === 'present')
                        ->schema([
                            Forms\Components\Group::make([
                                TextInput::make('tajweed_score')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('من 100')
                                    ->label('التجويد')
                                    ->required(fn (Get $get): bool => $get('recitationSession.present') === 'present'),
                    
                                TextInput::make('fluency_score')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('من 100')
                                    ->label('الأداء')
                                    ->required(fn (Get $get): bool => $get('recitationSession.present') === 'present'),
                    
                                TextInput::make('memory_score')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('من 100')
                                    ->label('الحفظ')
                                    ->required(fn (Get $get): bool => $get('recitationSession.present') === 'present'),
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

        return $table
            ->columns([
                TextColumn::make('recitationSession.session_date')
                    ->label('تاريخ الجلسة')
                    ->date('Y-m-d')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('recitationSession.halaka.name')
                    ->label('الحلقة')
                    ->searchable(),

                TextColumn::make('recitationSession.student.user.name')
                    ->label('الطالب')
                    ->searchable(),


                TextColumn::make('recitationSession.present')
                    ->label('الحضور')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        'present' => 'حاضر',
                        'absent_with_excuse' => 'غائب بعذر',
                        'absent_without_excuse' => 'غائب بدون عذر',
                        default => 'غير معروف',
                    })
                    ->color(fn ($state): string => match ($state) {
                        'present' => 'success',
                        'absent_with_excuse' => 'warning',
                        'absent_without_excuse' => 'danger',
                        default => 'secondary',
                    }),


                TextColumn::make('surah_name')
                    ->label('سورة النهاية')
                    ->toggleable(),

                TextColumn::make('ayah_text')
                    ->label('آية النهاية')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('pages')
                    ->label('عدد الاوجه')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('recitationSession.evaluationScore')
                ->label('التقييم')
                ->badge()
                ->color('info')

            ])
            ->actions([
                Action::make('تعديل')
                    ->icon('heroicon-o-pencil')
                    ->url(fn($record) => AlMaqraaRecitationResource::getUrl('edit', ['record' => $record])),

                Action::make('نتائج-الجلسة')
                    ->visible(fn(AlMaqraaRecitation $record) => $record->recitationSession->present === 'present')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn($record) => AlMaqraaRecitationResource::getUrl('edit', ['record' => $record]) . '?tab=-actual-results-tab'),
            ])
            ->headerActions([
                Action::make('generate_pdf')
                    ->label('تصدير تقرير PDF')
                    ->icon('icon-halaka')
                    ->color('success')
                    ->form([
                        Select::make('time_range')
                            ->label('الفترة الزمنية')
                            ->options([
                                'monthly' => 'التقرير الشهري',
                                'yearly' => 'التقرير السنوي',
                                'custom' => 'تحديد نطاق زمني',
                            ])
                            ->required()
                            ->live(),
                        Select::make('month')
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
                            ->visible(fn($get) => $get('time_range') === 'monthly')
                            ->required(fn($get) => $get('time_range') === 'monthly'),
                        DatePicker::make('start_date')
                            ->label('من تاريخ')
                            ->visible(fn($get) => $get('time_range') === 'custom')
                            ->required(fn($get) => $get('time_range') === 'custom'),

                        DatePicker::make('end_date')
                            ->label('إلى تاريخ')
                            ->visible(fn($get) => $get('time_range') === 'custom')
                            ->required(fn($get) => $get('time_range') === 'custom'),

                         Forms\Components\CheckboxList::make('teachers')
                            ->label('المعلمين')
                            ->options(Teacher::where('program_type','maqraa')->pluck('name','id'))
                            ->columns(2)
                            ->bulkToggleable() 
                            ->searchable(),
                        Forms\Components\Select::make('freeze_students')
                            ->label('تجميد الطلبة')
                            ->options(
                                Student::whereHas('candidate', fn($q) => $q->where('status', 'accepted')->where('program_type', 'maqraa'))
                                    ->with('candidate') // Eager load the candidate relation
                                    ->get()
                                    ->mapWithKeys(fn($student) => [
                                        $student->id => $student->candidate->full_name ?? 'N/A'
                                    ])
                            )
                            ->multiple()
                            ->searchable(),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('recitations.pdf-download-almaqraa-report', [
                            'time_range' => $data['time_range'],
                            'month' => $data['month'] ?? null,
                            'start_date' => $data['start_date'] ?? null,
                            'end_date' => $data['end_date'] ?? null,
                            'teachers' => $data['teachers'] ?? [],
                            'freeze_students' => $data['freeze_students'] ?? [],
                        ]);
                    })
            ])->filters([
                
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
                                        ->whereHas('candidate', fn($q) => $q->where('status', 'accepted')->where('program_type', 'maqraa'))
                                        ->with('candidate')
                                        ->get()
                                        ->mapWithKeys(fn($student) => [$student->id => $student->candidate->full_name])
                                        ->toArray();
                                }
                                return Student::query()
                                    ->whereHas('candidate', fn($q) => $q->where('status', 'accepted')->where('program_type', 'maqraa'))
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
            ])->filtersFormColumns(3)->filtersLayout(FiltersLayout::AboveContent);
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
