<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\halaka;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\QuranPageCalculator;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\SessionsResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class SessionsResource extends Resource implements HasShieldPermissions
{

    protected static ?string $model = Halaka::class;
    protected static ?string $navigationIcon = 'icon-halaka';
    protected static ?string $navigationLabel = 'الحلقات';

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete'];
    }



    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([

            Section::make()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('اسم الحلقة')
                        ->required(),

                    Select::make('teacher_id')
                        ->relationship('teacher', 'name')
                        ->label('المعلم')
                        ->preload()
                        ->searchable()
                        ->required(),

                    DatePicker::make('start_date')
                        ->label('تاريخ البداية')
                        ->required(),

                ])
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('id')->label('المعرف')->sortable(),

            TextColumn::make('name')->label('اسم الحلقة')->sortable()->searchable(),

            TextColumn::make('teacher.name')->label('المعلم')->sortable(),

            TextColumn::make('start_date')
                ->label('تاريخ البداية')
                ->date()
                ->sortable(),

            TextColumn::make('max_students')->label('الحد الأقصى')->sortable(),

            TextColumn::make('students_count')
                ->label('عدد الطلاب')
                ->sortable()
                ->getStateUsing(fn(Halaka $record) => $record->getStudentsCountAttribute())
                ->badge()
                ->color('warning'),

            TextColumn::make('status')
                ->label('حالة الحلقة')
                ->formatStateUsing(fn($state) => $state === 'مكتمل' ? 'مكتمل' : 'غير مكتمل')
                ->color(fn($state) => $state === 'مكتمل' ? 'danger' : 'success'),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('المعلم')
                    ->relationship('teacher', 'name'),
            ])
            ->actions([
                EditAction::make(), // ✔️ تأكد من أنك تستعمل `EditAction`
                //DeleteAction::make()->visible(fn($record) => auth()->user()->can('delete', $record)), 
            ])
            ->bulkActions([
                DeleteBulkAction::make(), // ✔️ تأكد من أنك تستعمل `DeleteBulkAction`
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // يمكن إضافة علاقة الطلاب لاحقًا
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSessions::route('/'),
            'create' => Pages\CreateSessions::route('/create'),
            'edit' => Pages\EditSessions::route('/{record}/edit'),
        ];
    }
}
