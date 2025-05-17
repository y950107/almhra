<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Candidate;
use Filament\Tables\Table;
use App\Enums\CandidateStatus;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use App\Jobs\ConvertCandidateToStudent;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\CandidateResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Illuminate\Validation\Rules\Can;

class CandidateResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Candidate::class;
    protected static ?string $navigationIcon = 'icon-condidates';

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any', 'accept', 'tointerview'];
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.candidate.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.candidate.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.candidate.plural_model_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Tabs::make(__('filament.candidate.tabs.details'))
                            ->tabs([
                                Tabs\Tab::make(__('filament.candidate.tabs.basic_information'))
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        Section::make(__('filament.candidate.sections.personal_details'))
                                            ->columns(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('full_name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label(__('filament.candidate.fields.full_name')),

                                                Forms\Components\TextInput::make('phone')
                                                    ->tel()
                                                    ->live()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->label(__('filament.candidate.fields.phone'))
                                                    ->prefix('+966'),

                                                Forms\Components\TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->label(__('filament.candidate.fields.email')),

                                                Forms\Components\DatePicker::make('birthdate')
                                                    ->required()
                                                    ->maxDate(Carbon::now()->subYears(settings("min_age",10)))
                                                    ->minDate(Carbon::now()->subYears(settings("max_age",70)))
                                                    ->label(__('filament.candidate.fields.birthdate')),

                                                Forms\Components\Select::make('qualification')
                                                    ->options(settings('qualifications'))
                                                    ->required()
                                                    ->label(__('filament.candidate.fields.qualification')),
                                            ]),
                                    ]),

                                Tabs\Tab::make(__('filament.candidate.tabs.quranic_information'))
                                    ->icon('heroicon-o-book-open')
                                    ->schema([
                                        Section::make(__('filament.candidate.sections.quranic_background'))
                                            ->columns()
                                            ->schema([
                                                Forms\Components\Select::make('quran_level')
                                                    ->options(Candidate::getQuranLevels())
                                                    ->required()
                                                    ->label(__('filament.candidate.fields.quran_level')),
                                                Forms\Components\Select::make('self_evaluation')
                                                    ->options([60, 70, 80, 90, 100])
                                                    ->label(__('filament.candidate.fields.self_evaluation')),
                                                Forms\Components\Select::make('desired_recitations')
                                                    ->options(settings("reading_types"))
                                                    ->multiple()
                                                    ->label(__('filament.candidate.fields.desired_recitations'))
                                                    ->columnSpanFull(),



                                                Forms\Components\Toggle::make('has_ijaza')
                                                    ->label(__('filament.candidate.fields.has_ijaza'))
                                                    ->live(),

                                                Forms\Components\CheckboxList::make('ijaza_types')
                                                    ->options(settings('ijaza_types'))
                                                    ->visible(fn(Forms\Get $get) => $get('has_ijaza'))
                                                    ->label(__('filament.candidate.fields.ijaza_types')),
                                            ]),
                                    ]),

                                Tabs\Tab::make(__('filament.candidate.tabs.documents'))
                                    ->icon('heroicon-o-document')
                                    ->schema([
                                        Section::make(__('filament.candidate.sections.attachments'))
                                            ->schema([
                                                Forms\Components\FileUpload::make('qualification_file')
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->directory('candidates/qualifications')
                                                    ->visibility('public')
                                                    ->downloadable()
                                                    ->openable()
                                                    ->required()
                                                    ->label(__('filament.candidate.fields.qualification_file')),

                                                Forms\Components\FileUpload::make('audio_recitation')
                                                    ->acceptedFileTypes(['audio/mpeg', 'audio/wav'])
                                                    ->directory('candidates/recitations')
                                                    ->maxSize(10240)
                                                    ->label(__('filament.candidate.fields.audio_recitation')),
                                            ]),
                                    ]),

                                Tabs\Tab::make(__('filament.candidate.tabs.administrative'))
                                    ->icon('heroicon-o-cog')
                                    ->schema([
                                        Section::make(__('filament.candidate.sections.administrative_details'))
                                            ->schema([
                                                Forms\Components\Select::make('teacher_id')
                                                    ->relationship('teacher', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->label(__('filament.candidate.fields.teacher')),

                                                Forms\Components\Select::make('status')
                                                    ->options([
                                                        'pending' => __('filament.candidate.status.pending'),
                                                        'interview' => __('filament.candidate.status.interview'),
                                                        'accepted' => __('filament.candidate.status.accepted'),
                                                        'rejected' => __('filament.candidate.status.rejected'),
                                                    ])
                                                    ->default('pending')
                                                    ->label(__('filament.candidate.fields.status')),
                                            ]),
                                    ]),
                            ])
                            ->columnSpanFull()
                            ->persistTabInQueryString(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label(__('filament.candidate.fields.full_name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label(__('filament.candidate.fields.email'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->label(__('filament.candidate.fields.phone'))->searchable()->sortable(),

                Tables\Columns\TextColumn::make('quran_level')
                    ->label(__('filament.candidate.fields.quran_level'))
                    ->formatStateUsing(fn($state) => __('filament.candidate.levels.' . $state))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('has_ijaza')
                    ->label(__('filament.candidate.fields.has_ijaza'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.candidate.fields.status'))
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn($state) => $state instanceof CandidateStatus ? __('filament.candidate.status.'.$state->value) : __('filament.candidate.status.unknown'))
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.candidate.fields.created_at'))
                    ->date()
                    ->toggleable(),

                Tables\Columns\ViewColumn::make('qualification_file')
                    ->label(__('filament.candidate.fields.qualification_file'))
                    ->view('tables.columns.file-embed'),

                Tables\Columns\ViewColumn::make('audio')
                    ->label(__('filament.candidate.fields.audio_recitation'))
                    ->view('tables.columns.audio-player')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.candidate.fields.status'))
                    ->options([
                        'pending' => __('filament.candidate.status.pending'),
                        'accepted' => __('filament.candidate.status.accepted'),
                        'rejected' => __('filament.candidate.status.rejected'),
                        'interview' => __('filament.candidate.status.interview'),
                    ]),

                SelectFilter::make('quran_level')
                    ->label(__('filament.candidate.fields.quran_level'))
                    ->options(Candidate::getQuranLevels()),

                TernaryFilter::make('has_ijaza')
                    ->label(__('filament.candidate.fields.has_ijaza'))
                    ->trueLabel(__('filament.general.yes'))
                    ->falseLabel(__('filament.general.no')),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label(__('filament.candidate.actions.accept'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn(Candidate $record) => acceptedCandidate($record))
                    ->requiresConfirmation()
                    ->visible(fn(Candidate $record) => auth()?->user()?->hasPermissionTo('accept_candidate') && $record?->status?->value === 'pending'),

                Tables\Actions\Action::make('sendToInterview')
                    ->label(__('filament.candidate.actions.send_to_interview'))
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn(Candidate $record) => sendToInterview($record))
                    ->color('primary')
                    ->visible(fn(Candidate $record) => auth()?->user()?->hasPermissionTo('tointerview_candidate') && $record?->status?->value !== 'accepted'),

                Tables\Actions\Action::make('viewDetails')
                    ->label(__('filament.candidate.actions.view_details'))
                    ->icon('heroicon-o-eye')
                    ->modalHeading(__('filament.candidate.actions.details'))
                    ->modalWidth('1xxl')
                    ->form([
                        Forms\Components\TextInput::make('full_name')->label(__('filament.candidate.fields.full_name'))->disabled(),
                        Forms\Components\TextInput::make('email')->label(__('filament.candidate.fields.email'))->disabled(),
                        Forms\Components\TextInput::make('phone')->label(__('filament.candidate.fields.phone'))->disabled(),
                        Forms\Components\TextInput::make('birthdate')->label(__('filament.candidate.fields.birthdate'))->disabled(),
                        Forms\Components\TextInput::make('qualification')->label(__('filament.candidate.fields.qualification'))->disabled(),
                        Forms\Components\Select::make('quran_level')
                            ->label(__('filament.candidate.fields.quran_level'))
                            ->options([
                                'beginner' => __('filament.candidate.levels.beginner'),
                                'intermediate' => __('filament.candidate.levels.intermediate'),
                                'advanced' => __('filament.candidate.levels.advanced'),
                            ])
                            ->disabled(),
                        Forms\Components\Toggle::make('has_ijaza')->label(__('filament.candidate.fields.has_ijaza'))->disabled(),
                        Forms\Components\TextInput::make('qualification_file')->label(__('filament.candidate.fields.qualification_file'))->disabled(),
                    ])
                    ->fillForm(fn(Candidate $record) => $record->only([
                        'full_name', 'email', 'phone', 'birthdate',
                        'qualification', 'quran_level', 'has_ijaza',
                        'qualification_file', 'audio_recitation',
                    ])),

                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCandidates::route('/'),
            'create' => Pages\CreateCandidate::route('/create'),
            'edit' => Pages\EditCandidate::route('/{record}/edit'),
        ];
    }
}
