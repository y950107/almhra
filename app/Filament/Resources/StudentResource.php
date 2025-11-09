<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Mpdf\Tag\Select;
use App\Models\Halaka;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\Candidate;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use IbrahimBougaoua\FilaProgress\Tables\Columns\CircleProgress;

class StudentResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'icon-students';

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.student.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.student.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.student.plural_model_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('teacher_id')
                    ->label('الشيخ')
                    ->relationship('teacher', 'name')
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->label('تاريخ الالتحاق')
                    ->required(),

                Forms\Components\Group::make()
                    ->relationship('candidate')
                    ->dehydrated()
                    ->statePath('candidate')
                    ->schema([
                        Forms\Components\Select::make('program_type')
                            ->label('البرنامج')
                            ->options(Candidate::getProgramTypes())
                            ->reactive()
                            ->live()
                            ->required(),
                    ]),

                Forms\Components\TextInput::make('maqraa_memorization_duration')
                    ->label('مدة الحفظ - برنامج المقراة')
                ->hidden(fn(Student $student) => $student->candidate->program_type !== 'maqraa'),

                Forms\Components\TextInput::make('mutqin_memorization_duration')
                    ->label('مدة الحفظ - برنامج المتقن')
                ->hidden(fn(Student $student) => $student->candidate->program_type !== 'mutqin'),

                Forms\Components\TextInput::make('mahir_memorization_duration')
                    ->label('مدة الحفظ - برنامج الماهر')
                ->hidden(fn(Student $student) => $student->candidate->program_type !== 'mahir'),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('monthly_target_pages')
                        ->label('عدد الاوجه الشهري')
                        ->numeric()
                        ->nullable(),
                        Forms\Components\Repeater::make('monthly_excepted_months_pages')
                        ->label('عدد الأوجه المستثناة من الشهور')
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

                Forms\Components\Section::make('التقدم الدراسي')
                    ->schema([
                        Forms\Components\TextInput::make('current_level')
                            ->label('المستوى الحالي')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                        Forms\Components\TextInput::make('total_pages')
                            ->label('الصفحات المحفوظة')
                            ->numeric()
                            ->disabled()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('start_date','desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('الاسم')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('البريد الالكتروني')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('candidate.program_type')
                    ->formatStateUsing(fn($state) => Candidate::getProgramTypes()[$state] ?? 'غير معروف')
                    ->label('البرنامج')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('الشيح')->sortable()->searchable()->badge()->color('danger'),

                Tables\Columns\TextColumn::make('present_sessions_percentage')
                    ->label('التسميع الحضوري')
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('online_sessions_percentage')
                    ->label('التسميع عن بعد')
                    ->suffix('%'),


                Tables\Columns\IconColumn::make('candidate.has_ijaza')
                    ->label('لديه إجازة')
                    ->boolean()->sortable()->toggleable(),


                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ الالتحاق')
                    ->badge()
                    ->color('success')
                    ->date('Y-m-d'),


                CircleProgress::make('progress_percentage')->label('نسبة الإنجاز')
                    ->getStateUsing(function ($record) {
                        $total = 100;
                        $progress = $record->getProgressPercentageAttribute();
                        return [
                            'total' => $total,
                            'progress' => $progress,
                        ];
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make('edit'),
                Tables\Actions\DeleteAction::make('delete'),
            ])->headerActions([
                Action::make('generate_presence_pdf')
                    ->label('تقرير الحضور و الغياب')
                    ->icon('icon-halaka')
                    ->color('success')
                    ->form([

                        DatePicker::make('start_date')
                            ->default(Carbon::now()->startOfYear())
                            ->label('من تاريخ'),

                        DatePicker::make('end_date')
                            ->default(Carbon::now()->endOfYear())
                            ->label('إلى تاريخ'),
                        Forms\Components\Select::make('program_type')
                            ->label('البرنامج')
                            ->options(Candidate::getProgramTypes())
                            ->reactive()
                            ->live(),
                        Forms\Components\CheckboxList::make('teachers')
                           ->label('اختر المعلم')
                            ->options(function (Get $get) {
                                return Teacher::when($get('program_type'), function ($query) use ($get) {
                                    $query->where('program_type', $get('program_type'));
                                })
                                ->pluck('name', 'id')
                                ->toArray();
                            })
                            ->columns(2)
                            ->bulkToggleable()
                            ->searchable(),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('students-presence.pdf-download', [
                            'start_date' => $data['start_date'] ?? null,
                            'end_date' => $data['end_date'] ?? null,
                            'teachers' => $data['teachers'] ?? [],
                        ]);
                    }),
                Action::make('generate_pdf')
                    ->label('تقرير الطلاب')
                    ->icon('icon-students')
                    ->color('info')
                    ->form([

                        DatePicker::make('start_date')
                            ->default(Carbon::now()->startOfYear())
                            ->label('من تاريخ'),

                        DatePicker::make('end_date')
                            ->default(Carbon::now()->endOfYear())
                            ->label('إلى تاريخ'),
                        Forms\Components\Select::make('program_type')
                            ->label('البرنامج')
                            ->options(Candidate::getProgramTypes())
                            ->reactive()
                            ->live(),
                        Forms\Components\CheckboxList::make('teachers')
                            ->label('اختر المعلم')
                            ->options(function (Get $get) {
                                return Teacher::when($get('program_type'), function ($query) use ($get) {
                                    $query->where('program_type', $get('program_type'));
                                })
                                ->pluck('name', 'id')
                                ->toArray();
                            })
                            ->columns(2)
                            ->bulkToggleable()
                            ->searchable(),

                    ])
                    ->action(function (array $data) {

                        return redirect()->route('students.pdf-download', [
                            'start_date' => $data['start_date'] ?? null,
                            'end_date' => $data['end_date'] ?? null,
                            'teachers' => $data['teachers'] ?? [],

                        ]);
                    })
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
