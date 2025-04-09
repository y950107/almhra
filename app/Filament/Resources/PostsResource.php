<?php

namespace App\Filament\Resources;


use App\Models\Post;
use Filament\Forms\Components\FileUpload;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\PostsResource\Pages;
use App\Models\RecitationSession;

class PostsResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'icon-blogs';
    public static function getNavigationGroup(): ?string
    {
        return __('filament.grouplabel');
    }

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.posts.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.posts.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.posts.plural_model_label');
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')->required()
                            ->label('العنوان')
                            ->live()
                            ->maxLength(255)
                            ->afterStateUpdated(function (callable $set, $state) {
                                $set('slug', str()->slug($state));
                            }),
                        TextInput::make('subtitle')->required()->maxLength(255)
                            ->label('العنوان الفرعي'),
                        TextInput::make('slug')->required()->disabled()->label('الرابط المخصص'),
                        Textarea::make('bio')->required()->label('الوصف')->maxLength(255),
                        RichEditor::make('body')->required()->maxLength(65535)->columnSpanFull()->label('النص'),
                        FileUpload::make('background')->required()->image()->label('الخلفية'),
                        TextInput::make('photo_alt_text')->required()->label('الوصف في حالة غياب الصورة')->maxLength(255),
                        Toggle::make('is_visible')->default(false)->required()->label('الحالة'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        // ->query(RecitationSession::where('student_id',auth()->id())->query())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('العنوان الفرعي')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('الرابط')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('background')
                    ->label('الخلفية')
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('is_visible')
                    ->label('الحالة'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePosts::route('/create'),
            'edit' => Pages\EditPosts::route('/{record}/edit'),
        ];
    }
}
