<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Evaluation;
use Filament\Tables\Table;
use App\Enums\EvaluationStatus;
use Tables\Columns\BadgeColumn;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\EvaluationResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class EvaluationResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Evaluation::class;
    protected static ?string $navigationIcon = 'icon-evaluation';


    public static function getNavigationLabel(): string
    {
        return __('filament.evaluations.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.evaluations.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.evaluations.plural_model_label');
    }
    public static function getNavigationBadge(): ?string
    {
        return cache()->remember('pending_evaluations_count', 60, function () {
            return (string) Evaluation::where('status', 'pending')->count();
        });
    }

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('candidate_id')
                    ->label('المترشح')
                    ->relationship('candidate', 'full_name')
                    ->required(),

                Forms\Components\Select::make('evaluator_id') //لازم تخدم على الاستاذ كانه مستخدم و ليس  idv الاستلذ 

                    ->label('المقيّم')
                    ->relationship('evaluator', 'name')
                    //->getOptionLabelFromRecordUsing(fn($record) => "{$record->name}")
                    ->required()
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Section::make('درجات التقييم')
                    ->schema([
                        Forms\Components\TextInput::make('tajweed_score')
                            ->label('التجويد')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100), //   لازم تخدم على الاستاذ كانه مستخدم و ليس  idv الاستلذ 

                        Forms\Components\TextInput::make('voice_score')
                            ->label('جودة الصوت')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),

                        Forms\Components\TextInput::make('memorization_score')
                            ->label('الحفظ')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                    ])
                    ->columns(3),

                Forms\Components\Textarea::make('notes')
                    ->label('ملاحظات')
                    ->nullable(),

                Forms\Components\Hidden::make('total_score')
                    ->label('المعدل')
                    ->default(fn($get) => ($get('tajweed_score') + $get('voice_score') + $get('memorization_score')) / 3),

                Forms\Components\Hidden::make('status')
                    ->label('الحالة')
                    ->default(fn($get) => ($get('total_score') >= 80 ? 'passed' : 'pending')),
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('candidate.full_name')
                    ->label('المترشح')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('evaluator.name')
                    ->label('المشرف')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tajweed_score')
                    ->label('درجة التجويد'),

                Tables\Columns\TextColumn::make('voice_score')
                    ->label('درجة الصوت'),

                Tables\Columns\TextColumn::make('memorization_score')
                    ->label('درجة الحفظ'),

                Tables\Columns\TextColumn::make('total_score')
                    ->label('المعدل')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->badge()
                    ->colors([
                        'success' => fn($state) => $state >= 80,
                        'warning' => fn($state) => $state < 80 && $state >= 50,
                        'danger'  => fn($state) => $state < 50
                    ])
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(EvaluationStatus $state) => $state->label())
                    ->badge()
                    ->color(fn(EvaluationStatus $state) => match ($state) {
                        EvaluationStatus::PENDING => 'warning',
                        EvaluationStatus::PASSED => 'success',
                        EvaluationStatus::FAILED => 'danger',
                    })
                    ->colors([
                        'pending' => 'warning',
                        'passed' => 'success',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('فلترة حسب الحالة')
                    ->options([
                        'pending' => 'احتياطي',
                        'passed' => 'مقبول',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('convert_to_student')
                    ->label('تحويل إلى طالب')
                    ->icon('heroicon-o-users')
                    ->action(fn(Evaluation $evaluation) => evaluateCandidate($evaluation))
                    ->requiresConfirmation()
                    ->visible(fn(Evaluation $evaluation) => auth()?->user()?->hasPermissionTo('accept_candidate') && in_array($evaluation?->status?->value, ['pending', 'failed'])),


                Tables\Actions\EditAction::make()
                    ->label('تقييم المترشح')

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
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
        ];
    }
}
