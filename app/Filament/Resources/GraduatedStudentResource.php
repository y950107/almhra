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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\GraduatedStudentResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use IbrahimBougaoua\FilaProgress\Tables\Columns\CircleProgress;
use App\Filament\Resources\GraduatedStudentResource\RelationManagers;

class GraduatedStudentResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'icon-graduated_student';
    protected static ?int $navigationSort = 2;
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.graduated_student.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.graduated_student.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.graduated_student.plural_model_label');
    }
    public static function canCreate(): bool
    {
        return false;
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

                Forms\Components\TextInput::make('monthly_target_pages')
                    ->label('عدد الاوجه الشهري')
                    ->numeric()
                    ->nullable(),


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
            ->modifyQueryUsing(function ($query) {
                $query->whereHas('halakas', function ($query) {
                    $query->where('moved_at', '!=', null)->where('finish_quran',  true);
                });
            })
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
                    ->formatStateUsing(fn(Student $student) => $student->currentHalakas()->latest()->first()->attend_at ?? Carbon::parse($student->start_date)->format('Y-m-d'))
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
            ->filters([
                Filter::make('from_date')
                ->label('من تاريخ')
                ->form([
                    DatePicker::make('from')->label('من تاريخ'),
                ])
                ->query(function ($query, array $data) {
                    if ($data['from']) {
                        $query->whereHas('halakas', function ($query) use ($data) {
                            $query->where('moved_at', '>=', $data['from']);
                        });
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
                            $query->whereHas('halakas', function ($query) use ($data) {
                                $query->where('moved_at', '<=', $data['to']);
                            });
                        }
                    })   ->indicateUsing(function (array $data): ?string {
                        return $data['to'] ? 'إلى: ' . \Carbon\Carbon::parse($data['to'])->format('Y-m-d') : null;
                    }),
            ])->filtersFormColumns(3)->filtersLayout(FiltersLayout::AboveContent)
            ->actions([
                // Tables\Actions\EditAction::make('edit'),
                // Tables\Actions\DeleteAction::make('delete'),
            ])
            ->headerActions([
                Action::make('generate_report')
                ->label('طباعة ')
                ->color('primary')
                ->action(function (array $data) {
                    return redirect()->route('graduated_students.pdf-download');
                }), 
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
            'index' => Pages\ListGraduatedStudents::route('/'),
            'create' => Pages\CreateGraduatedStudent::route('/create'),
            'edit' => Pages\EditGraduatedStudent::route('/{record}/edit'),
        ];
    }
}
