<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\AlMaherRecitation;
use App\Models\AlMaqraaRecitation;
use App\Models\AlMutqinRecitation;
use App\Models\Candidate;
use App\Models\Student;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('الاسم')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('candidate.birthdate')
                    ->label('تاريخ الميلاد')
                    ->date('Y-m-d'),

                Tables\Columns\TextColumn::make('candidate.email')
                    ->label('البريد الالكتروني')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('candidate.program_type')
                    ->formatStateUsing(fn($state) => Candidate::getProgramTypes()[$state] ?? 'غير معروف')
                    ->label('البرنامج')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('candidate.has_ijaza')
                    ->label('لديه إجازة')
                    ->boolean()->sortable()->toggleable(),


                Tables\Columns\TextColumn::make('present_percentage')
                    ->label('التسميع الحضوري')
                    ->getStateUsing(function (Student $record) {
                        $program_type = $record->candidate->program_type;

                        if ($program_type === 'maqraa') {
                            $present = AlMaqraaRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present')
                                ->where('recitation_type','in_person');
                            })->count();

                            $total = AlMaqraaRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present');
                            })->count();

                        }
                        else if ($program_type === 'mutqin') {
                            $present = AlMutqinRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present')
                                    ->where('recitation_type','in_person');
                            })->count();

                            $total = AlMutqinRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present');
                            })->count();
                        }
                        else {
                            $present = AlMaherRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present')
                                    ->where('recitation_type','in_person');
                            })->count();

                            $total = AlMaherRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present');
                            })->count();
                        }


                        return $total > 0 ? (int) round(($present / $total) * 100, 0) : 0;
                    })->suffix('%'),

                Tables\Columns\TextColumn::make('online_percentage')
                    ->label('التسميع عن بعد')
                    ->getStateUsing(function (Student $record) {
                        $program_type = $record->candidate->program_type;

                        if ($program_type === 'maqraa') {
                            $present = AlMaqraaRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present')
                                    ->where('recitation_type','remote');
                            })->count();

                            $total = AlMaqraaRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present');
                            })->count();

                        }
                        else if ($program_type === 'mutqin') {
                            $present = AlMutqinRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present')
                                    ->where('recitation_type','remote');
                            })->count();

                            $total = AlMutqinRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present');
                            })->count();
                        }
                        else {
                            $present = AlMaherRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present')
                                    ->where('recitation_type','remote');
                            })->count();

                            $total = AlMaherRecitation::whereHas('recitationSession', function (Builder $query) use($record) {
                                $query->where('student_id',$record->id)->where('present','=','present');
                            })->count();
                        }


                        return $total > 0 ? (int) round(($present / $total) * 100, 0) : 0;
                    })->suffix('%'),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('الشيح')->sortable()->searchable()->badge()->color('success'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ الالتحاق')
                    ->date('Y-m-d'),


            ])
            ->actions([
                Tables\Actions\EditAction::make('edit'),
                Tables\Actions\DeleteAction::make('delete'),
            ])->headerActions([
                Action::make('generate_pdf')
                    ->label('تصدير تقرير PDF')
                    ->icon('icon-halaka')
                    ->color('success')
                    ->form([

                        DatePicker::make('start_date')
                            ->label('من تاريخ'),

                        DatePicker::make('end_date')
                            ->label('إلى تاريخ'),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('students.pdf-download', [
                            'start_date' => $data['start_date'] ?? null,
                            'end_date' => $data['end_date'] ?? null,
                        ]);
                    })
            ]);;
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
