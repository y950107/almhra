<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Blogs;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BlogsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BlogsResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class BlogsResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Blogs::class;
    public static function getNavigationGroup(): ?string
    {
        return __('filament.grouplabel');
    }
    protected static ?string $navigationIcon = 'icon-blogs';

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }
    public static function getNavigationLabel(): string
    {
        return __('filament.ads.navigation_label');
    }
   
    public static function getModelLabel(): string
    {
        return __('filament.ads.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.ads.plural_model_label');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
               Forms\Components\Section::make()
               ->columns([
                TextInput::make('title')
                    ->label('العنوان')
                    ->maxLength(400)
                    ->required(),

                TextInput::make('bio')
                    ->label('وصف مختصر')
                    ->nullable(),

                FileUpload::make('background')
                    ->columnSpanFull()
                    ->label('صورة الخلفية')
                    ->image()
                    ->required(),
                ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('العنوان')->searchable()->sortable(),
                TextColumn::make('bio')->label('الوصف')->limit(50),
                ImageColumn::make('background')->label('الخلفية'),
                TextColumn::make('created_at')->label('تاريخ الإضافة')->date('Y-m-d')->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlogs::route('/create'),
            'edit' => Pages\EditBlogs::route('/{record}/edit'),
        ];
    }
}
