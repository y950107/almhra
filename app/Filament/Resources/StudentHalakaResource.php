<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Halaka;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Tables\Filters\DateFilter;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\StudentHalakaResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class StudentHalakaResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Halaka Management';
    protected static ?string $navigationLabel = 'Student Halaka Registration';
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'restore',
            'force_delete',
        ];
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make('Student Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('user.name')
                            ->label('Student Name')
                            ->required()
                            ->disabled(),

                        TextInput::make('user.email')
                            ->label('Email')
                            ->required()
                            ->disabled(),
                    ]),

                Section::make('Halaka Registration')
                    ->columns(2)
                    ->schema([
                        Select::make('halaka_id')
                            ->relationship('halakas', 'name')
                            ->label('Halaka')
                            ->required()
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->optionLabel(fn (Halaka $record): string => $record->name . ' - ' . $record->location),

                        DatePicker::make('halaka_student.attend_at')
                            ->label('Attendance Date')
                            ->default(now())
                            ->required(),

                        DatePicker::make('halaka_student.moved_at')
                            ->label('Moved Date')
                            ->nullable()
                            ->default(null),
                    ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currentHalakas.name')
                    ->label('Current Halakas')
                    ->getStateUsing(fn (Student $record): string => 
                        $record->currentHalakas->pluck('name')->implode(', '))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('halaka_student.attend_at')
                    ->label('Attendance Date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('halaka_student.moved_at')
                    ->label('Moved Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('halaka')
                    ->relationship('halakas', 'name')
                    ->multiple()
                    ->label('Halaka')
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('moved_at')
                    ->label('Current Status')
                    ->trueLabel('Current Students')
                    ->falseLabel('Former Students')
                    ->placeholder('All Students'),

                // DateFilter::make('halaka_student.attend_at')
                //     ->label('Attendance Date'),

                // DateFilter::make('halaka_student.moved_at')
                //     ->label('Moved Date'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn($record) => auth()->user()->can('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn() => auth()->user()->can('delete_any', Student::class)),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentHalakas::route('/'),
            'create' => Pages\CreateStudentHalaka::route('/create'),
            'edit' => Pages\EditStudentHalaka::route('/{record}/edit'),
        ];
    }
}
