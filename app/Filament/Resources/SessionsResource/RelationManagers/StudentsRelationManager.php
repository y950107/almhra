<?php

namespace App\Filament\Resources\SessionsResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Halaka;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Notifications\Notification; // For notifications
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class StudentsRelationManager extends RelationManager implements HasShieldPermissions
{
    protected static string $relationship = 'students';
    protected static ?string $title = 'الطلاب';
    protected static ?string $label = 'طالب';
    protected static ?string $pluralLabel = 'طلاب';
    public static function getNavigationLabel(): string
    {
        return __('filament.student_halakas.navigation_label');
    }

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('student_id')
            ->relationship('students', 'user.name')
            ->label('الطالب')
            ->preload()
            ->disabled(fn() => $this->isReadOnly())
            ->disabledOn('edit') 
            ->required()
            ->options(function () {
                return Student::query()
                    ->select('students.id', 'users.name as label')
                    ->join('users', 'students.user_id', '=', 'users.id')
                    ->pluck('label', 'students.id');
            })
            ,

            DatePicker::make('attend_at')
                ->label('تاريخ الالتحاق')
                ->default(now())
                ->required(),

            DatePicker::make('moved_at')
                ->label('تاريخ الانتقال')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('اسم الطالب')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('البريد الالكتروني')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attend_at')
                    ->label('تاريخ الالتحاق')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('moved_at')
                    ->label('تاريخ التجميد')
                    ->date('Y-m-d')
                    ->sortable(),
                BadgeColumn::make('finish_quran')
                    ->label('ختم القرآن')
                    ->state(fn($record) => $record->finish_quran ? 'ختم القرآن' : 'لا')
                    ->color(fn($record) => $record->finish_quran ? 'success' : 'danger')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('moved_at')
                    ->label('الحالة')
                    ->trueLabel('الطلاب الحاليين')
                    ->falseLabel('الطلاب السابقين')
                    ->placeholder('جميع الطلاب'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('إضافة طالب')
                ->modalHeading('إضافة طالب إلى الحلقة')
                ->modalSubmitActionLabel('إضافة')
                ->modalCancelActionLabel('إلغاء')
                ->closeModalByClickingAway(false)
                ->successNotificationTitle('تم إضافة الطالب إلى الحلقة بنجاح')
                ->visible(fn() => auth()->user()->can('create', Student::class))
                ->action(function (array $data) {
                    // Get the student and halaka
                    $student = Student::findOrFail($data['student_id']);
                    $halaka = Halaka::findOrFail($this->ownerRecord->id);
                    
                    // Attach student to halaka with pivot data
                    $halaka->students()->attach($student->id, [
                        'attend_at' => $data['attend_at'],
                        'moved_at' => $data['moved_at'] ?? null
                    ]);
                })
                ->form([
                    Select::make('student_id')
                        ->relationship('students', 'user.name')
                        ->label('الطالب')
                        ->preload()
                        ->searchable()
                        ->required()
                        ->options(function () {
                            return Student::query()
                                ->select('students.id', 'users.name as label')
                                ->join('users', 'students.user_id', '=', 'users.id')
                                ->pluck('label', 'students.id');
                        }),
                    
                    DatePicker::make('attend_at')
                        ->label('تاريخ الالتحاق')
                        ->default(now())
                        ->required(),
                    
                    DatePicker::make('moved_at')
                        ->label('تاريخ الانتقال')
                        ->nullable(),
                ])
            ])
            ->actions([
                Tables\Actions\Action::make('finish_quran')
                ->label(fn($record) => $record->finish_quran ? 'إلغاء ختم القرآن' : 'ختم القرآن')
                ->color(fn($record) => $record->finish_quran ? 'danger' : 'success')
                ->icon(fn($record) => $record->finish_quran ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-closed') 
                // Appropriate icon forfreeze action
                ->modalHeading(fn($record) => $record->finish_quran ? 'إلغاء ختم القرآن للطالب في الحلقة' : 'تأكيد ختم القرآن للطالب في الحلقة')
                ->modalSubmitActionLabel(fn($record) => $record->finish_quran ? 'تأكيد الغاء الختم' : 'تأكيد الختم')
                ->modalCancelActionLabel('إلغاء ')
                ->closeModalByClickingAway(false)
                ->requiresConfirmation()
                ->successNotificationTitle(fn($record) => $record->finish_quran ? 'تم الغاء الختم للطالب بنجاح' : 'تم تأكيد الختم للطالب بنجاح')
                ->visible(fn($record) => auth()->user()->can('update', $record))
                ->action(function (Student $record) {
                    // if($record->moved_at == null){
                    //     Notification::make()
                    //     ->title('لا يمكن اضافة ختم القرءان للطالب ')
                    //     ->body('لا يمكن  اضافة ختم القرءان للطالب لأنه لا يزال في الحلقة')
                    //     ->danger()
                    //     ->send();
                    //     return;
                    // }
                    $this->getOwnerRecord()->students()->updateExistingPivot($record->id, [
                        'finish_quran' => $record->finish_quran ? false : true
                    ]);
                }),
                EditAction::make()
                    ->closeModalByClickingAway(false)
                    ->label('نقل لحلقة اخرى')
                    ->action(function (Student $record, array $data) {
                       try {
                        DB::beginTransaction();
                        
                        // 1. First attach to new halaka
                        $newHalaka = Halaka::findOrFail($data['halaka_id']);
                        $newHalaka->students()->attach($record->id, [
                            'attend_at' => $data['attend_at'] ?? now(),
                            'moved_at' => null
                        ]);
                        
                        // 2. Then detach from current halaka
                        $this->ownerRecord->students()->detach($record->id);
                        DB::commit();
                        
                        Notification::make()
                            ->title('تم النقل بنجاح')
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        DB::rollBack();
                        
                        Notification::make()
                            ->title('حدث خطأ أثناء النقل')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                            
                        throw $e; // Re-throw to show error in form
                    }
                })
                 ->form([
                    Select::make('halaka_id')
                        ->label('الحلقة')
                        ->options(function () {
                            return Halaka::query()
                                ->where('halaka_status', true)
                                ->where('id', '!=', $this->ownerRecord->id)
                                ->pluck('name', 'id');
                        })
                        ->required(),
                    DatePicker::make('attend_at')
                        ->label('تاريخ الالتحاق'),
                        
                ]),
             Tables\Actions\Action::make('freeze')
                    ->label('تجميد')
                    ->color('danger')
                    ->icon('heroicon-o-lock-closed') 
                    // Appropriate icon forfreeze action
                    ->modalHeading('تجميد الطالب في الحلقة')
                    ->modalSubmitActionLabel('تأكيد التجميد')
                    ->modalCancelActionLabel('إلغاء')
                    ->closeModalByClickingAway(false)
                    ->requiresConfirmation()
                    ->successNotificationTitle('تم تجميد الطالب بنجاح')
                    ->visible(fn($record) => auth()->user()->can('update', $record))
                    ->action(function (Student $record) {
                        $this->getOwnerRecord()->students()->updateExistingPivot($record->id, [
                            'moved_at' => now()
                        ]);
                    })
                    ->visible(fn($record) => auth()->user()->can('delete', $record) && $record->moved_at==null),
             Tables\Actions\Action::make('unfreeze')
                    ->label('رفع التجميد')
                    ->color('success')
                    ->icon('heroicon-o-lock-closed') 
                    // Appropriate icon forfreeze action
                    ->modalHeading('رفع التجميد عن الطالب في الحلقة')
                    ->modalSubmitActionLabel('تأكيد رفع التجميد')
                    ->modalCancelActionLabel('إلغاء')
                    ->closeModalByClickingAway(false)
                    ->requiresConfirmation()
                    ->successNotificationTitle('تم  رفع تجميد عن الطالب بنجاح')
                    ->visible(fn($record) => auth()->user()->can('delete', $record) && $record->moved_at != null)
                    ->action(function (Student $record) {
                        $this->getOwnerRecord()->students()->updateExistingPivot($record->id, [
                            'moved_at' => null
                        ]);
                    }),
                Tables\Actions\Action::make('detach')
                    ->label('فصل')
                    ->color('primary')
                    ->icon('heroicon-o-trash')
                    ->closeModalByClickingAway(false)
                    ->requiresConfirmation()
                    ->action(function (Student $record) {
                        $halaka = Halaka::findOrFail($this->ownerRecord->id);
                        
                        // Detach student from halaka
                        $halaka->students()->detach($record->id);
                    })
                    ->visible(fn($record) => auth()->user()->can('delete', $record))
            ])
            ->bulkActions([
                // DeleteBulkAction::make()
                //     ->visible(fn() => auth()->user()->can('delete_any', Student::class)),
            ]);
    }
}
