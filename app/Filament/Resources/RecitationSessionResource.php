<?php

namespace App\Filament\Resources;

use Tabs\Tab;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\RecitationSession;
use App\Models\recitation_sessions;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RecitationSessionResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\RecitationSessionResource\RelationManagers;
use Filament\Tables\Filters\Filter;

class RecitationSessionResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }
    protected static ?string $model = RecitationSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->columns(2)
                ->schema([
                    Tabs::make('تفاصيل الجلسة')->tabs([
                        // 
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
                            TextInput::make('target_percentage')->numeric()->label('المعدل المستهدف')->required(),
                        ]),

                        // 
                        Tabs\Tab::make('الأهداف القرآنية')->schema([
                            TextInput::make('start_surah')->label('سورة البداية')->required(),
                            TextInput::make('start_ayah')->numeric()->label('آية البداية')->required(),
                            TextInput::make('start_page')->numeric()->label('صفحة البداية')->required(),
                            TextInput::make('end_surah')->label('سورة النهاية')->required(),
                            TextInput::make('end_ayah')->numeric()->label('آية النهاية')->required(),
                            TextInput::make('end_page')->numeric()->label('صفحة النهاية')->required(),
                        ])->columns(2),

                        // 
                        Tabs\Tab::make('النتائج الفعلية')->schema([
                            TextInput::make('actual_end_surah')->label('سورة النهاية الفعلية'),
                            TextInput::make('actual_end_ayah')->numeric()->label('آية النهاية الفعلية'),
                            TextInput::make('actual_end_page')->numeric()->label('صفحة النهاية الفعلية'),
                        ]),

                        // 
                        Tabs\Tab::make('تقييمات المعلم')->schema([

                            Toggle::make('present_status')
                                ->label('حالة الحضور')
                                ->onColor('success')
                                ->offColor('danger'),

                            TextInput::make('tajweed_score')->numeric()->label('درجة التجويد')->disabled(fn($get) => !$get('present_status')),
                            TextInput::make('fluency_score')->numeric()->label('درجة الطلاقة')->disabled(fn($get) => !$get('present_status')),
                            TextInput::make('memory_score')->numeric()->label('درجة الحفظ')->disabled(fn($get) => !$get('present_status')),
                        ]),


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

            TextColumn::make('session_date')->label('تاريخ الجلسة')->dateTime()->toggleable(),
            TextColumn::make('halaka.name')->label('الحلقة')->toggleable(),
            TextColumn::make('student.candidate.full_name')->label('الطالب')->toggleable(),
            TextColumn::make('start_surah')->label('سورة البداية')->toggleable(),
            TextColumn::make('end_surah')->label('سورة النهاية')->toggleable(),
            TextColumn::make('target_pages')
                ->label('الاوجه المستهدفة')
                ->badge()
                ->colors([
                    'success'
                ])->toggleable(),
            TextColumn::make('actual_pages')
                ->label('الاوجه المخققة')
                ->badge()
                ->colors([
                    'warning'
                ])->toggleable(),
            TextColumn::make('target_percentage')
                ->label('المستهدف')
                ->getStateUsing(fn($record) => $record->target_percentage . '%')
                ->badge()
                ->colors([
                    'success'
                ])->toggleable(),
            TextColumn::make('achievement_percentage')
                ->label('المحقق')
                ->formatStateUsing(callback: fn($state) => $state . '%')
                ->badge()
                ->colors([
                    'success' => fn($state) => $state >= 80,
                    'warning' => fn($state) => $state < 80 && $state >= 50,
                    'danger'  => fn($state) => $state < 50
                ])->toggleable(),
            IconColumn::make('present_status')
                ->label('حالة الحضور')
                ->boolean()->toggleable(),

            // TextColumn::make('weighted_achievement')
            //     ->label('التقييم الوزني')
            //     ->getStateUsing(fn($record) => $record->weighted_achievement)
            //     ->badge()
            //     ->colors([
            //         'success' => fn($state) => $state >= 80,
            //         'warning' => fn($state) => $state < 80 && $state >= 50,
            //         'danger'  => fn($state) => $state < 50
            //     ])

        ])

            ->actions([
                //EditAction::make(),

                Action::make('نتائج-الجلسة/ تعديل')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn($record) => RecitationSessionResource::getUrl('edit', ['record' => $record]) . '#tab-النتائج الفعلية')
                //->openUrlInNewTab(),



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
