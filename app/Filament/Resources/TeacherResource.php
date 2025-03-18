<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Teacher;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\URL;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\TeacherResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;
    protected static ?string $navigationIcon = 'icon-teachers';

    public static function getNavigationLabel(): string
    {
        return __('filament.teacher.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.teacher.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.teacher.plural_model_label');
    }
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }
    //     public static function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('تحميل PDF')
    //             ->icon('icon-halaka')
    //             ->color('success')
    //             ->url(fn () => URL::route('teachers.pdf-preview'), true)
    //             ->openUrlInNewTab(), // ✅ فتح الرابط في تبويب جديد لعرض المعاينة
    //     ];
    // }

    //     public static function generatePdf()// hadi khdamtha b dompdf
    // {
    //     $teachers = Teacher::all(); // جلب جميع المعلمات

    //     $pdf = Pdf::loadView('pdf.teachers', compact('teachers')); // تحميل بيانات المعلمات داخل قالب PDF

    //     return response()->streamDownload(
    //         fn () => print($pdf->output()),
    //         'قائمة-المعلمات.pdf'
    //     );
    // }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->label('الهاتف')
                            ->tel()
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('المؤهلات')
                    ->schema([
                        Forms\Components\TagsInput::make('qualifications')
                            ->label('الشهادات العلمية')
                            ->splitKeys([',']),
                        Forms\Components\TagsInput::make('expertise')
                            ->label('الايجازات ')
                            ->splitKeys([',']),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        //dd();
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف'),

                TextColumn::make('qualifications')
                    ->label('المؤهلات')
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : ($state ? $state : 'لا توجد إجازات'))
                    ->badge()
                    ->color('info')
                    ->searchable(),


                Tables\Columns\TextColumn::make('expertise')
                    ->label('الايجازات')
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : ($state ? $state : 'لا توجد إجازات'))
                    ->badge()
                    ->color('warning')
                    ->searchable(),


                /* TextColumn::make('user_id')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => $state ? 'نشط' : 'غير نشط')
                    ->getStateUsing(fn(Teacher $record) => $record->hasAccount() ? 'active' : 'inactive'), */





                TextColumn::make('user.name')
                    ->label('الحساب المرتبط')
                    ->placeholder('بدون حساب')
                    ->badge()
                    ->color(fn($state) => filled($state) ? 'success' : 'danger'),

                /* Action::make('createAccount')
                    ->label('إنشاء حساب')
                    ->icon('heroicon-o-user-plus')
                    ->requiresConfirmation()
                    ->action(fn(Teacher $record) => CreateTeacherUser::dispatchSync($record))
                    ->hidden(fn(Teacher $record) => !is_null($record->user_id)), */

            ])
            ->actions([
                Action::make('createAccount')
                    ->label('إنشاء حساب')
                    ->icon('heroicon-o-user-plus')
                    ->requiresConfirmation()
                    ->action(fn(Teacher $record) => TeacherToUser($record))

                    ->hidden(fn(Teacher $record) => $record->user_id !== null), // 

            ])
            ->headerActions([
                Action::make('generate_pdf')
                ->label('تصدير PDF')
                ->icon('icon-halaka')
                ->color('success')
                ->form([
                    CheckboxList::make('teacher_ids')
                        ->label('اختر المعلمين')
                        ->options(Teacher::pluck('name', 'id')->toArray())
                        ->columns(2)
                        ->bulkToggleable() // ✅ خاصية "تحديد الجميع"
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    return redirect()->route('teachers.pdf-download', ['teacher_ids' => $data['teacher_ids']]);
                }), ]);

        /* ->actions([
                Tables\Actions\Action::make('activate')
                    ->label('تفعيل الحساب')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn($record) => $record->update(['status' => 'active']))
                    ->hidden(fn($record) => $record->status === 'active')
            ]); */
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
