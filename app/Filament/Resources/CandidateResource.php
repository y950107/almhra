<?php

namespace App\Filament\Resources;

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
                        Tabs::make('Candidate Details')
                            ->tabs([
                                Tabs\Tab::make('Basic Information')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        Forms\Components\Section::make('Personal Details')
                                            ->columns(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('full_name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label('Full Name'),

                                                Forms\Components\TextInput::make('phone')
                                                    ->tel()
                                                    ->live()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->label('رقم الجوال ')
                                                    ->prefix('+966')
                                                    ->mask('5 9999 9999'),


                                                Forms\Components\TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->label('Email Address'),

                                                Forms\Components\DatePicker::make('birthdate')
                                                    ->required()
                                                    ->maxDate(now()->subYears(10))
                                                    ->label('Birth Date'),

                                                Forms\Components\Select::make('qualification')
                                                    ->options([
                                                        'bac' => 'باكالوريا',
                                                        'licence' => 'ليسانس',
                                                        'master' => 'ماستر',
                                                        'doctorate' => 'دكتوراه',
                                                    ])
                                                    ->required()
                                                    ->label('Academic Qualification'),
                                            ]),
                                    ]),

                                Tabs\Tab::make('Quranic Information')
                                    ->icon('heroicon-o-book-open')
                                    ->schema([
                                        Forms\Components\Section::make('Quranic Background')
                                            ->columns(3)
                                            ->schema([
                                                Forms\Components\Select::make('quran_level')
                                                    ->options([
                                                        'beginner' => 'مبتدئ',
                                                        'intermediate' => 'متوسط',
                                                        'advanced' => 'متقدم',
                                                    ])
                                                    ->required()
                                                    ->label('Current Level'),

                                                Forms\Components\Select::make('desired_recitations')
                                                    ->options([
                                                        'hafs' => 'حفص',
                                                        'warsh' => 'ورش',
                                                        'qalun' => 'قالون',
                                                        'tajweed' => 'تجويد',
                                                        'complete' => 'ختمة كاملة',
                                                    ])
                                                    ->multiple()
                                                    ->label('Desired Recitation Types')
                                                    ->columnSpanFull(),

                                                Forms\Components\Select::make('self_evaluation')
                                                    ->options([
                                                       50,70,80,90,100
                                                    ])
                                                    ->label('Self Evaluation'),

                                                Forms\Components\Toggle::make('has_ijaza')
                                                    ->label('Has Ijaza?')
                                                    ->live(),

                                                Forms\Components\CheckboxList::make('ijaza_types')
                                                    ->options([
                                                        'hafs' => 'حفص',
                                                        'warsh' => 'ورش',
                                                        'qalun' => 'قالون',
                                                        'duri' => 'الدوري',
                                                    ])
                                                    ->visible(fn(Forms\Get $get) => $get('has_ijaza'))
                                                    ->label('Ijaza Types'),
                                            ]),
                                    ]),

                                Tabs\Tab::make('Documents')
                                    ->icon('heroicon-o-document')
                                    ->schema([
                                        Forms\Components\Section::make('Attachments')
                                            ->schema([
                                                Forms\Components\FileUpload::make('qualification_file')
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->directory('candidates/qualifications')
                                                    ->downloadable()
                                                    ->openable()
                                                    ->label('Qualification Document'),

                                                Forms\Components\FileUpload::make('audio_recitation')
                                                    ->acceptedFileTypes(['audio/mpeg', 'audio/wav'])
                                                    ->directory('candidates/recitations')
                                                    ->maxSize(10240) // 10MB
                                                    ->label('Recitation Sample (MP3/WAV)'),
                                            ]),
                                    ]),

                                Tabs\Tab::make('Administrative')
                                    ->icon('heroicon-o-cog')
                                    ->schema([
                                        Forms\Components\Section::make('Administrative Details')
                                            ->schema([
                                                Forms\Components\Select::make('teacher_id')
                                                    ->relationship('teacher', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->label('Assigned Teacher'),

                                                Forms\Components\Select::make('status')
                                                    ->options([
                                                        'pending' => 'Pending',
                                                        'interview' => 'Interview',
                                                        'accepted' => 'Accepted',
                                                        'rejected' => 'Rejected',
                                                    ])
                                                    ->default('pending')
                                                    ->label('Application Status'),
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
                Tables\Columns\TextColumn::make('full_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->searchable()->formatStateUsing(fn ($state) => preg_replace('/^(\+?\d{3})(\d{3})(\d{4})$/', '$1 $2 $3', $state))->sortable(),

                Tables\Columns\TextColumn::make('quran_level')
                    ->label('مستوى الحفظ')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'beginner' => 'مبتدئ',
                        'intermediate' => 'متوسط',
                        'advanced' => 'متقدم',
                        default => $state,
                    })->sortable()->toggleable(),

                Tables\Columns\IconColumn::make('has_ijaza')
                    ->label('لديه إجازة')
                    ->boolean()->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->sortable()->toggleable()
                    ->formatStateUsing(fn($state) => $state instanceof CandidateStatus ? $state->value : 'غير معروف')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->date()->toggleable(),

                Tables\Columns\ViewColumn::make('qualification_file')
                    ->label('ملف المؤهل')
                    ->view('tables.columns.file-embed'),

                Tables\Columns\ViewColumn::make('audio')
                    ->label('التسجيل الصوتي')
                    ->view('tables.columns.audio-player')
                    ->sortable()
                    ->searchable(),

            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('حالة الطلب')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'accepted' => 'مقبول',
                        'rejected' => 'مرفوض',
                        'interview' => 'في المقابلة',
                    ]),

                SelectFilter::make('quran_level')
                    ->label('مستوى الحفظ')
                    ->options([
                        'beginner' => 'مبتدئ',
                        'intermediate' => 'متوسط',
                        'advanced' => 'متقدم',
                    ]),

                TernaryFilter::make('has_ijaza')
                    ->label('لديه إجازة')
                    ->trueLabel('نعم')
                    ->falseLabel('لا'),
            ])

            ->actions([

                Tables\Actions\Action::make('accept')
                    ->label('قبول')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn(Candidate $record) => acceptedCandidate($record))
                    ->requiresConfirmation()
                    ->visible(fn(Candidate $record) => auth()?->user()?->hasPermissionTo('accept_candidate') && $record?->status?->value === 'pending'),

                Tables\Actions\Action::make('sendToInterview')
                    ->label('إرسال إلى المقابلة')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn(Candidate $record) => sendToInterview($record))
                    ->color('primary')
                    ->visible(fn(Candidate $record) => auth()?->user()?->hasPermissionTo('tointerview_candidate') && $record?->status?->value !== 'accepted'),



                Tables\Actions\Action::make('viewDetails')
                    ->label('عرض التفاصيل')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('تفاصيل المترشح')
                    ->modalWidth('1xxl')
                    ->form([
                        Forms\Components\TextInput::make('full_name')->label('الاسم الكامل')->disabled(),
                        Forms\Components\TextInput::make('email')->label('البريد الإلكتروني')->disabled(),
                        Forms\Components\TextInput::make('phone')->label('رقم الهاتف')->disabled(),
                        Forms\Components\TextInput::make('birthdate')->label('تاريخ الميلاد')->disabled(),
                        Forms\Components\TextInput::make('qualification')->label('المؤهل الدراسي')->disabled(),
                        Forms\Components\Select::make('quran_level')
                            ->label('مستوى الحفظ')
                            ->options([
                                'beginner' => 'مبتدئ',
                                'intermediate' => 'متوسط',
                                'advanced' => 'متقدم',
                            ])
                            ->disabled(),
                        Forms\Components\Toggle::make('has_ijaza')->label('لديه إجازة')->disabled(),
                        Forms\Components\TextInput::make('qualification_file')->label('ملف المؤهل')->disabled(),

                    ])
                    ->fillForm(fn(Candidate $record) => [
                        'full_name' => $record->full_name,
                        'email' => $record->email,
                        'phone' => $record->phone,
                        'birthdate' => $record->birthdate,
                        'qualification' => $record->qualification,
                        'quran_level' => $record->quran_level,
                        'has_ijaza' => $record->has_ijaza,
                        'qualification_file' => $record->qualification_file,
                        'audio_recitation' => $record->audio_recitation,
                    ]),

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
