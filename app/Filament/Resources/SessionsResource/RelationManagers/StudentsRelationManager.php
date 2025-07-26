<?php

namespace App\Filament\Resources\SessionsResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Halaka;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class StudentsRelationManager extends RelationManager implements HasShieldPermissions
{
    protected static string $relationship = 'students';
    protected static ?string $title = 'الطلاب';
    protected static ?string $label = 'طالب';
    protected static ?string $pluralLabel = 'طلاب';
    public static function getNavigationLabel(): string
    {
        return __('filament.student_halakas.navigation_label');
    }

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('student_id')
            ->relationship('students', 'user.name')
            ->label('الطالب')
            ->preload()
            ->disabled(fn() => $this->isReadOnly())
            ->required()
            ->options(function () {
                return Student::query()
                    ->select('students.id', 'users.name as label')
                    ->join('users', 'students.user_id', '=', 'users.id')
                    ->pluck('label', 'students.id');
            })
            ,

            DatePicker::make('attend_at')
                ->label('تاريخ الالتحاق')
                ->default(now())
                ->required(),

            DatePicker::make('moved_at')
                ->label('تاريخ الانتقال')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('اسم الطالب')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('البريد الالكتروني')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attend_at')
                    ->label('تاريخ الالتحاق')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('moved_at')
                    ->label('تاريخ الانتقال')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('moved_at')
                    ->label('الحالة')
                    ->trueLabel('الطلاب الحاليين')
                    ->falseLabel('الطلاب السابقين')
                    ->placeholder('جميع الطلاب'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('إضافة طالب')
                ->modalHeading('إضافة طالب إلى الحلقة')
                ->modalSubmitActionLabel('إضافة')
                ->modalCancelActionLabel('إلغاء')
                ->closeModalByClickingAway(false)
                ->successNotificationTitle('تم إضافة الطالب إلى الحلقة بنجاح')
                ->visible(fn() => auth()->user()->can('create', Student::class))
                ->action(function (array $data) {
                    // Get the student and halaka
                    $student = Student::findOrFail($data['student_id']);
                    $halaka = Halaka::findOrFail($this->ownerRecord->id);
                    
                    // Attach student to halaka with pivot data
                    $halaka->students()->attach($student->id, [
                        'attend_at' => $data['attend_at'],
                        'moved_at' => $data['moved_at'] ?? null
                    ]);
                })
                ->form([
                    Select::make('student_id')
                        ->relationship('students', 'user.name')
                        ->label('الطالب')
                        ->preload()
                        ->searchable()
                        ->required()
                        ->options(function () {
                            return Student::query()
                                ->select('students.id', 'users.name as label')
                                ->join('users', 'students.user_id', '=', 'users.id')
                                ->pluck('label', 'students.id');
                        }),
                    
                    DatePicker::make('attend_at')
                        ->label('تاريخ الالتحاق')
                        ->default(now())
                        ->required(),
                    
                    DatePicker::make('moved_at')
                        ->label('تاريخ الانتقال')
                        ->nullable(),
                ])
            ])
            ->actions([
                EditAction::make()
                    ->closeModalByClickingAway(false)
                    ->label('تعديل'),
                Tables\Actions\Action::make('detach')
                    ->label('فصل')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->closeModalByClickingAway(false)
                    ->requiresConfirmation()
                    ->action(function (callable $get) {
                        $student = Student::findOrFail($get('student_id'));
                        $halaka = Halaka::findOrFail($this->ownerRecord->id);
                        
                        // Detach student from halaka
                        $halaka->students()->detach($student->id);
                    })
                    ->visible(fn($record) => auth()->user()->can('delete', $record)),
            ])
            ->bulkActions([
                // DeleteBulkAction::make()
                //     ->visible(fn() => auth()->user()->can('delete_any', Student::class)),
            ]);
    }
}
