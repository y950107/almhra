<?php

namespace App\Filament\Resources;

use Tabs\Tab;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\RecitationSession;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\RecitationSessionResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\RecitationSessionResource\RelationManagers;
use App\Services\Moshaf_madina_Service;

class RecitationSessionResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }
    protected static ?string $model = RecitationSession::class;

    protected static ?string $navigationIcon = 'icon-recitations';

    public static function getNavigationLabel(): string
    {
        return __('filament.recitationsesion.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.recitationsesion.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.recitationsesion.plural_model_label');
    }

    public static function form(Form $form): Form
    {

        $quranService = new Moshaf_madina_Service();
        return $form->schema([
            Section::make()
                ->columns(2)
                ->schema([
                    Tabs::make('تفاصيل الجلسة')->tabs([

                        //  المعلومات الأساسية
                        Tabs\Tab::make('المعلومات الأساسية')->schema([
                            Select::make('halaka_id')
                                ->relationship('halaka', 'name')
                                ->label('الحلقة')
                                ->required(),

                            Select::make('student_id')
                                ->relationship('student.candidate', 'full_name')
                                ->label('الطالب')
                                ->unique(table: 'recitation_sessions', column: 'student_id', ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                                    return $rule->where('halaka_id', $get('halaka_id'));
                                })
                                ->validationMessages([
                                    'unique' => 'تم تحويل الطالب لهذه الحصة بالفعل، يرجى اختيار حصة تسميع آخر!',
                                ])
                                ->required(),

                            DatePicker::make('session_date')
                                ->label('تاريخ الجلسة')
                                ->required(),

                            TextInput::make('target_percentage')
                                ->numeric()
                                ->label('المعدل المستهدف')
                                ->required(),
                        ]),

                        //  الأهداف القرآنية
                        Tabs\Tab::make('الأهداف القرآنية')->schema([
                            Select::make('start_surah_id')
                                ->label('سورة البداية')
                                ->options(fn() => collect($quranService->getSurahs())->pluck('name', 'id'))
                                ->reactive()
                                ->live()
                                ->afterStateUpdated(fn($get, $set) => $set('start_ayah_id', null)),

                            Select::make('start_ayah_id')
                                ->label('آية البداية')
                                ->options(
                                    fn($get) => $get('start_surah_id')
                                        ? collect($quranService->getAyahs($get('start_surah_id')))
                                        ->mapWithKeys(function ($ayah) {
                                            return [
                                                $ayah['number'] => "{$ayah['number']} - {$ayah['text']}" 
                                            ];
                                        })
                                        : []
                                )
                                ->reactive()
                                ->live()
                                ->searchable()
                                
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
                                                $ayah['number'] => "{$ayah['number']} - {$ayah['text']}" 
                                            ];
                                        })
                                        : []
                                )
                                ->reactive()
                                ->live()
                                ->searchable()
                                ->afterStateUpdated(function ($get, $set) use ($quranService) {
                                    // حساب عدد الأسطر
                                    $targetLines = $quranService->calculateLines(
                                        $get('start_surah_id'),
                                        $get('start_ayah_id'),
                                        $get('end_surah_id'),
                                        $get('end_ayah_id')
                                    );
                                    $set('target_lines', $targetLines);
                                    if ($get('end_surah_id') && $get('end_ayah_id')) {
                                        $startPage = $quranService->getStartPageByAyah($get('end_surah_id'), $get('end_ayah_id'));
                                        $set('end_page', $startPage);
                                    };
                                    // حساب عدد الصفحات
                                    $startSurahId = $get('start_surah_id');
                                    $targetPages = 0;

                                    if ($startSurahId == 1) { 
                                        $targetLines -= 7; 
                                        $targetPages = ceil($targetLines / 15) + 1; 
                                    } else {
                                        $targetPages = ceil($targetLines / 15); 
                                    }
                                    $targetPages = number_format($targetPages, 2);
                                    $set('target_pages', $targetPages); 
                                }),

                                TextInput::make('start_page')
                                ->label('صفحة البداية')
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                                TextInput::make('end_page')
                                ->label('صفحة النهاية')
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            TextInput::make('target_lines')
                                ->label(' عدد الاسطر المستهدفة')
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            TextInput::make('target_pages')
                                ->label('عدد الصفحات المستهدفة')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(),
                        ]),

                        //  النتائج الفعلية
                        Tabs\Tab::make('النتائج الفعلية')->schema([
                            Select::make('actual_end_surah_id')
                                ->label('سورة النهاية الفعلية')
                                ->options(fn() => collect($quranService->getSurahs())->pluck('name', 'id'))
                                ->reactive()
                                ->live()
                                ->afterStateUpdated(fn($get, $set) => $set('actual_end_ayah_id', null)),

                            Select::make('actual_end_ayah_id')
                                ->label('آية النهاية الفعلية')
                                ->options(
                                    fn($get) => $get('actual_end_surah_id')
                                        ? collect($quranService->getAyahs($get('actual_end_surah_id')))
                                        ->mapWithKeys(function ($ayah) {
                                            return [
                                                $ayah['number'] => "{$ayah['number']} - {$ayah['text']}" 
                                            ];
                                        })
                                        : []
                                )
                                ->reactive()
                                ->live()
                                ->searchable()
                                ->afterStateUpdated(function ($get, $set) use ($quranService) {
                                    // حساب عدد الأسطر
                                    $actuelLines = $quranService->calculateLines(
                                        $get('start_surah_id'),
                                        $get('start_ayah_id'),
                                        $get('actual_end_surah_id'),
                                        $get('actual_end_ayah_id')
                                    );
                                    $set('actuel_lines', $actuelLines);

                                    if ($get('actual_end_surah_id') && $get('actual_end_ayah_id')) {
                                        $startPage = $quranService->getStartPageByAyah($get('actual_end_surah_id'), $get('actual_end_ayah_id'));
                                        $set('actual_end_page', $startPage);
                                    };
                                    // حساب عدد الصفحات
                                    $startSurahId = $get('start_surah_id');
                                    $targetPages = 0;

                                    if ($startSurahId == 1) {
                                        $actuelLines -= 7; 
                                        $targetPages = ($actuelLines / 15) + 1; 
                                    } else {
                                        $targetPages = ($actuelLines / 15); 
                                    }
                                    $targetPages = number_format($targetPages, 2);
                                    $set('actual_pages', $targetPages); 
                                }),

                                TextInput::make('actual_end_page')
                                ->label('صفحة النهاية الفعلية')
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            TextInput::make('actuel_lines')
                                ->label(' عدد الاسطر الفعلية')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(),

                            TextInput::make('actual_pages')
                                ->label('عدد الصفحات المستهدفة')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(),
                        ]),

                        // تقييمات المعلم
                        Tabs\Tab::make('تقييمات المعلم')->schema([
                            Toggle::make('present_status')
                                ->label('حالة الحضور')
                                ->onColor('success')
                                ->offColor('danger')
                                ->live(), 

                            TextInput::make('tajweed_score')
                                ->numeric()
                                ->label('درجة التجويد')
                                ->required(fn($get) => $get('present_status'))
                                ->disabled(fn($get) => !$get('present_status')),

                            TextInput::make('fluency_score')
                                ->numeric()
                                ->label('درجة الطلاقة')
                                ->required(fn($get) => $get('present_status'))
                                ->disabled(fn($get) => !$get('present_status')),

                            TextInput::make('memory_score')
                                ->numeric()
                                ->label('درجة الحفظ')
                                ->required(fn($get) => $get('present_status'))
                                ->disabled(fn($get) => !$get('present_status')),
                        ]),
                        //  الملاحظات العامة
                        Tabs\Tab::make('الملاحظات العامة')->schema([
                            Textarea::make('evaluation_notes')->label('ملاحظات المعلم'),
                            Textarea::make('notes')->label('ملاحظات عامة'),
                        ])
                    ])->columnSpanFull()
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('session_date')->label('تاريخ الجلسة')->date('Y-m-d')->toggleable(),
            TextColumn::make('halaka.name')->label('الحلقة')->toggleable(),
            TextColumn::make('student.candidate.full_name')->label('الطالب')->toggleable(),
            TextColumn::make('start_surah_name')->label('سورة البداية')->toggleable(),
            TextColumn::make('start_ayah_text')->label('آية البداية')->limit(30)->toggleable(),
            TextColumn::make('start_page')->label('صفحة البداية')->toggleable(),
            TextColumn::make('end_surah_name')->label('سورة النهاية')->toggleable(),
            TextColumn::make('end_ayah_text')->label('آية النهاية')->limit(30)->toggleable(),
            TextColumn::make('end_page')->label('صفحة النهاية')->toggleable(),
            TextColumn::make('actual_end_ayah_text')->label('آية محققة')->limit(30)->toggleable(),
            TextColumn::make('actual_end_page')->label('صفحة المحققة')->toggleable(),
            TextColumn::make('target_percentage')
                ->label('المستهدف')
                ->formatStateUsing(fn($state) => $state . '%')
                ->badge()
                ->colors(['success'])
                ->toggleable(),
            TextColumn::make('achievement_percentage')
                ->label('المحقق')
                ->formatStateUsing(fn($state) => $state . '%')
                ->badge()
                ->colors([
                    'success' => fn($state) => $state >= 80,
                    'warning' => fn($state) => $state < 80 && $state >= 50,
                    'danger'  => fn($state) => $state < 50
                ])
                ->toggleable(),
        ])
            ->filters([
                SelectFilter::make('halaka_id')->label('الحلقة')->relationship('halaka', 'name'),
                //DateFilter::make('session_date')->label('تاريخ الجلسة'),
            ])
            ->actions([
                Action::make('تعديل')
                    ->icon('heroicon-o-pencil')
                    ->url(fn($record) => RecitationSessionResource::getUrl('edit', ['record' => $record])),

                Action::make('نتائج-الجلسة')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn($record) => RecitationSessionResource::getUrl('edit', ['record' => $record]) . '#tab-النتائج الفعلية'),
            ])->headerActions([
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

                        DatePicker::make('start_date')
                            ->label('من تاريخ')
                            ->visible(fn($get) => $get('time_range') === 'custom')
                            ->required(fn($get) => $get('time_range') === 'custom'),

                        DatePicker::make('end_date')
                            ->label('إلى تاريخ')
                            ->visible(fn($get) => $get('time_range') === 'custom')
                            ->required(fn($get) => $get('time_range') === 'custom'),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('recitations.pdf-download', [
                            'time_range' => $data['time_range'],
                            'start_date' => $data['start_date'] ?? null,
                            'end_date' => $data['end_date'] ?? null,
                        ]);
                    }),

                // ->action(function (array $data) {
                //     return redirect()->route('recitation.pdf-download', ['teacher_ids' => $data['teacher_ids']]);
                // }),
            ]);
    }

    private static function calculateLines(Moshaf_madina_Service $quranService, $get)
    {
        if ($get('start_surah_id') && $get('start_ayah_id') && $get('end_surah_id') && $get('end_ayah_id')) {
            return $quranService->calculateLines(
                $get('start_surah_id'),
                $get('start_ayah_id'),
                $get('end_surah_id'),
                $get('end_ayah_id')
            );
        }
        return 0;
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
            'index' => Pages\ListRecitationSessions::route('/'),
            'create' => Pages\CreateRecitationSession::route('/create'),
            'edit' => Pages\EditRecitationSession::route('/{record}/edit'),
        ];
    }
}
