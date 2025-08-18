<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Halaka;
use Filament\Forms\Form;
use App\Models\Candidate;
use App\Models\Evaluation;
use Filament\Tables\Table;
use App\Enums\EvaluationStatus;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
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
            return (string)Evaluation::where('status', 'pending')->count();
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
                    ->relationship('candidate', 'full_name',modifyQueryUsing: function (Builder $query) {
                        $query->whereStatus('interview')->whereDoesntHave('student');
                    })
                    ->disabledOn('edit')
                    ->required(),


                Forms\Components\Select::make('evaluator_id')
                    ->label('المقيّم')
                    ->relationship('evaluator', 'name',function (Builder $query) {
                        $query->where('type','!=','student');
                    })
                    ->default(auth()->id())
                    ->afterStateHydrated(function (Forms\Components\Select $component, ?string $state) {
                        if ($state === null) {
                            $component->state(auth()->id());
                        }
                    })
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                Forms\Components\Section::make('درجات التقييم')
                    ->schema([
                        Forms\Components\TextInput::make('tajweed_score')
                            ->label('التجويد')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100), //   لازم تخدم على الاستاذ كانه مستخدم و ليس  idv الاستلذ

                        Forms\Components\TextInput::make('voice_score')
                            ->label('الأداء')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100),

                        Forms\Components\TextInput::make('memorization_score')
                            ->label('الحفظ')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100)
                    ])
                    ->columns(3),

                Forms\Components\Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull()
                    ->rows(6)
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

                Tables\Columns\TextColumn::make('candidate.email')
                    ->label('البريد الالكتروني')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('candidate.program_type')
                    ->formatStateUsing(fn($state) => Candidate::getProgramTypes()[$state] ?? 'غير معروف')
                    ->label('البرنامج')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('evaluator.name')
                    ->label('المقيم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tajweed_score')
                    ->label(' التجويد'),

                Tables\Columns\TextColumn::make('voice_score')
                    ->label(' الأداء'),

                Tables\Columns\TextColumn::make('memorization_score')
                    ->label(' الحفظ'),

                Tables\Columns\TextColumn::make('total_score')
                    ->label('المعدل')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->badge()
                    ->colors([
                        'success' => fn($state) => $state >= 80,
                        'warning' => fn($state) => $state < 80 && $state >= 50,
                        'danger' => fn($state) => $state < 50
                    ])
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->sortable()
                    ->formatStateUsing(fn(EvaluationStatus $state) => $state->label())
                    ->badge()
                    ->color(fn(EvaluationStatus $state) => match ($state) {
                        EvaluationStatus::PENDING => 'warning',
                        EvaluationStatus::PASSED => 'success',
                        EvaluationStatus::FAILED => 'danger',
                    }),
            ])
            ->filters([
                // Filter by date from
                Filter::make('from_date')
                    ->label('من تاريخ')
                    ->form([
                        DatePicker::make('from')->label('من تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['from']) {
                            $query->where('created_at', '>=', $data['from']);
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
                            $query->where('created_at', '<=', $data['to']);
                        }
                    })   ->indicateUsing(function (array $data): ?string {
                        return $data['to'] ? 'إلى: ' . \Carbon\Carbon::parse($data['to'])->format('Y-m-d') : null;
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->label('فلترة حسب الحالة')
                    ->options(
                        collect(EvaluationStatus::cases())->mapWithKeys(fn($status) => [
                            $status->value => $status->label(),
                        ])->toArray()
                    ),
            ])->filtersFormColumns(3)->filtersLayout(FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('convert_to_student')
                    ->label('تحويل إلى طالب')
                    ->icon('heroicon-o-users')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('halaka_id')
                        ->options(Halaka::all()->pluck('name', 'id'))
                        ->label('اختر الحلقة')
                        ->required()
                        ->preload()
                    ])
                    ->action(fn(Evaluation $evaluation, array $data) => evaluateCandidate($evaluation, $data))  
                    ->requiresConfirmation()
                    ->visible(fn(Evaluation $evaluation) => auth()?->user()?->hasPermissionTo('accept_candidate') && $evaluation->candidate?->student === null),


                Tables\Actions\EditAction::make()
                    ->label('تقييم المترشح'),

                Tables\Actions\DeleteAction::make()

            ])
            ->headerActions([
                Action::make('generate_pdf')
                ->label('تصدير PDF')
                ->icon('icon-halaka')
                ->color('success')
                // ->form([
                //     CheckboxList::make('evaluations_id')
                //     ->label('اختر مقابلات التقييم')
                //     ->options(
                //         Evaluation::with(['candidate' => fn($query) => $query->select('id', 'full_name')])->get()
                //     )
                //     ->getOptionLabelFromRecordUsing(function (Evaluation $record) {
                //         return $record->candidate->full_name ?? 'Unknown'; // Access through relationship
                //     })
                //     ->columns(2)
                //     ->bulkToggleable()
                //     ->searchable()
                // ])
                ->action(function (array $data) {
                    return redirect()->route('evaluations.pdf-download');
                }), 
                Action::make('generate_report')
                ->label('تقرير مختصر')
                ->color('primary')
                ->action(function (array $data) {
                    return redirect()->route('evaluations.pdf-download-short');
                }), 
            ])
            ;
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
