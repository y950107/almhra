<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Candidate;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Jobs\ConvertCandidateToStudent;
use App\Filament\Resources\CandidateResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class CandidateResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Candidate::class;
    protected static ?string $navigationIcon = 'icon-condidates';

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any', 'accept_candidate'];
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
                Forms\Components\Section::make('المعلومات الأساسية')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('full_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\DatePicker::make('birthdate')
                            ->required(),

                        Forms\Components\Select::make('qualification')
                            ->options([
                                'bac' => 'باكالوريا',
                                'licence' => 'ليسانس',
                                'master' => 'ماستر',
                            ]),
                    ]),



                Forms\Components\Section::make('المعلومات القرآنية')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('quran_level')
                            ->options([
                                'beginner' => 'مبتدئ',
                                'intermediate' => 'متوسط',
                                'advanced' => 'متقدم',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('has_ijaza'),
                        Forms\Components\CheckboxList::make('ijaza_types')
                            ->options([
                                'hafs' => 'حفص',
                                'warsh' => 'ورش',
                                'qalun' => 'قالون',
                            ])

                    ]),

                Forms\Components\Section::make('المرفقات')
                    ->schema([
                        Forms\Components\FileUpload::make('qualification_file')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('qualifications')
                            ->openable(),
                        Forms\Components\FileUpload::make('audio_recitation')
                            // mp3 ...... forma  nta3ha men ba3d nzidalha 
                            ->directory('recitations'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status') // هنا جبتها شغل  enum
                    ->formatStateUsing(function ($state) {
                        // نجيبها و نحولها لstring
                        if ($state instanceof \App\Enums\CandidateStatus) {
                            return $state->value;
                        }
                        // وإلا رجع القيمة كما هي
                        return $state;
                    }),

            ])

            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('قبول')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn(Candidate $record) => ConvertCandidateToStudent::dispatch($record))
                    ->requiresConfirmation()
                    ->visible(function (User $user, Candidate $record) {
                        dump($user->can('accept_candidate')); 
                       
                    }),

                // Tables\Actions\Action::make('عرض-التفاصيل')
                //     ->modalHeading('تفاصيل الطالب')
                //     ->modalCloseButton( true)
                //     ->modalWidth('2xl') 
                //     ->icon('heroicon-o-eye')
                //     ->button() 
                //     ->color('primary')
                //     ->form([
                //         Forms\Components\TextInput::make('name') 
                //             ->label('الاسم')
                //             ->disabled(),
                //         Forms\Components\TextInput::make('email')
                //             ->label('البريد الإلكتروني')
                //             ->disabled(),
                //         Forms\Components\TextInput::make('phone')
                //             ->label('رقم الهاتف')
                //             ->disabled(),
                //     ])
                //     ->fillForm(fn(Candidate $record) => [
                //         'name' => $record->name,
                //         'email' => $record->email,
                //         'phone' => $record->phone,
                //     ]),

                Tables\Actions\Action::make('sendToInterview')
                    ->label('إرسال إلى المقابلة')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn(Candidate $record) => sendToInterview($record))
                    ->color('primary')
                    ->visible(fn($record) => $record->status==='accepted'),


                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCandidates::route('/'),
            'create' => Pages\CreateCandidate::route('/create'),
            'edit' => Pages\EditCandidate::route('/{record}/edit'),
        ];
    }
}
