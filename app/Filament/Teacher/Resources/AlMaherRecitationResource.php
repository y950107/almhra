<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\AlMaherRecitationResource\Pages;
use App\Filament\Teacher\Resources\AlMaherRecitationResource\RelationManagers;
use App\Models\AlMaherRecitation;
use App\Models\Halaka;
use App\Models\RecitationSession;
use App\Services\Moshaf_madina_Service;
use App\Settings\GeneralSettings;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AlMaherRecitationResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    protected static ?string $slug = "al-maher";

    protected static ?string $model = AlMaherRecitation::class;

    protected static ?string $navigationIcon = 'icon-recitations';

    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->teacher->program_type === 'mahir';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.almaher-recitation.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.almaher-recitation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.almaher-recitation.plural_model_label');
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
                                                    $q->where('program_type', 'mahir');
                                                }),
                                            )
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
                                            ->options([
                                                'present' => 'حاضر',
                                                'absent_with_excuse' => 'غائب بعذر',
                                                'absent_without_excuse' => 'غائب بدون عذر',
                                            ])->live()
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
                                            ->options(settings('reading_types',[]))
                                            ->visible(fn (Forms\Get $get): bool => $get('present') === 'present'),
                                    ])->relationship('recitationSession')->columns()->statePath('recitationSession')->dehydrated(),

                                ]),

                            //  الأهداف القرآنية
                            Tabs\Tab::make('نتائج الحصة')
                                ->visible(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                ->schema([
                                    Select::make('start_surah_id')
                                        ->label('سورة البداية (اخر حصة)')
                                        ->options(fn() => collect($quranService->getSurahs())->pluck('name', 'id'))
                                        ->reactive()
                                        ->live()
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
                                        }),


                                    Select::make('end_surah_id')
                                        ->label('سورة النهاية')
                                        ->options(fn() => collect($quranService->getSurahs())->pluck('name', 'id'))
                                        ->reactive()
                                        ->live()
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
                                            // حساب عدد الأسطر
                                            $targetLines = $quranService->calculateLines(
                                                $get('start_surah_id'),
                                                $get('start_ayah_id'),
                                                $get('end_surah_id'),
                                                $get('end_ayah_id')
                                            );
                                            if ($get('end_surah_id') && $get('end_ayah_id')) {
                                                $startPage = $quranService->getStartPageByAyah($get('end_surah_id'), $get('end_ayah_id'));
                                                $set('end_page', $startPage);
                                            };
                                            // حساب عدد الصفحات
                                            $startSurahId = $get('start_surah_id');


                                            if ($startSurahId == 1) {
                                                $targetLines -= 7;
                                                $targetPages = ceil($targetLines / 15) + 1;
                                            } else {
                                                $targetPages = ceil($targetLines / 15);
                                            }
                                            $targetPages = number_format($targetPages, 2);
                                            $set('pages', $targetPages);
                                        }),

                                    TextInput::make('start_page')
                                        ->label('صفحة البداية')
                                        ->numeric()
                                        ->disabled(),

                                    TextInput::make('end_page')
                                        ->label('صفحة النهاية')
                                        ->numeric()
                                        ->disabled(),


                                    TextInput::make('pages')
                                        ->label('عدد الاوجه')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(),
                                ]),

                            Tabs\Tab::make('المتن المصاحب')
                                ->visible(fn (Forms\Get $get): bool => $get('recitationSession.present') === 'present')
                                ->schema([
                                    Select::make('lesson_title')
                                        ->label('حفظ المتون')
                                        ->options(AlMaherRecitation::getLessonTitles())
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


                                    TextInput::make('target_percentage')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->suffix('%')
                                        ->label('المعدل المستهدف'),

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
            ->query(
                AlMaherRecitation::query()->whereHas('recitationSession.halaka.teacher.user', function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            )
            ->columns([
                TextColumn::make('recitationSession.session_date')
                    ->label('تاريخ الجلسة')
                    ->date('Y-m-d')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('recitationSession.halaka.name')
                    ->label('الحلقة')
                    ->toggleable(),

                TextColumn::make('recitationSession.student.candidate.full_name')
                    ->label('الطالب')
                    ->toggleable(),

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

            ])
            ->actions([
                Action::make('تعديل')
                    ->icon('heroicon-o-pencil')
                    ->url(fn($record) => AlMaherRecitationResource::getUrl('edit', ['record' => $record])),

                Action::make('نتائج-الجلسة')
                    ->visible(fn(AlMaherRecitation $record) => $record->recitationSession->present === 'present')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn($record) => AlMaherRecitationResource::getUrl('edit', ['record' => $record]) . '?tab=-actual-results-tab'),
            ])->filters([
                // Filter by student
                SelectFilter::make('student_id')
                    ->label('الطالب')
                    ->relationship('recitationSession.student.candidate', 'full_name'),

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
            ])->filtersLayout(FiltersLayout::AboveContent);
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
