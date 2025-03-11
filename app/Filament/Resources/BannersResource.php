<?php

namespace App\Filament\Resources;


use Filament\Tables;
use App\Models\banner;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\BannersResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class BannersResource extends Resource implements HasShieldPermissions
{

    protected static ?string $model = banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
                ->label('العنوان')
                ->maxLength(400)
                ->required(),

            TextInput::make('subtitle')
                ->label('العنوان الفرعي')
                ->nullable(),

            FileUpload::make('background')
                ->label('الخلفية')
                ->image()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('العنوان')->searchable(),
            TextColumn::make('subtitle')->label('العنوان الفرعي')->searchable(),
            ImageColumn::make('background')->label('الخلفية'),
            TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime(),
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanners::route('/create'),
            'edit' => Pages\EditBanners::route('/{record}/edit'),
        ];
    }
}
