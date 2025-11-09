<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\RecitationSession;
use App\Models\Student;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
class AttendanceResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'icon-checklist';
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }
    protected static ?int $navigationSort = 10;
     public static function getNavigationLabel(): string
    {
        return __('filament.attendance.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.attendance.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.attendance.navigation_label');
    }
    public static function canCreate(): bool
    {
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Section::make()
               ->columns(2)
               ->schema([
                    Forms\Components\DatePicker::make('date')
                        ->label('التاريخ')
                        ->default(now())
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('حالة الحضور')
                        ->options(RecitationSession::getPresentOptions())
                        ->required(),
                   Forms\Components\CheckboxList::make('student_id')
                    ->options(
                        Student::with('candidate')->get()->mapWithKeys(function ($student) {
                            return [$student->id => $student->candidate->full_name];
                        })->toArray()
                    )
                    ->bulkToggleable()
                    ->columnSpanFull()
                    ->columns(3)
                    ->searchable()
                    ->label('الطالب')
                    ->required(),
                    
                   
                    Forms\Components\Textarea::make('notes')
                        ->label('ملاحظات')
                        ->columnSpanFull(), 
                  ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('الاسم')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('candidate.phone')
                    ->label('رقم الهاتف')->sortable(),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('الشيخ')->sortable()->searchable()->badge()->color('danger'),
                Tables\Columns\TextColumn::make('status')
                ->color(function (Student $student, $livewire) {
                    $date = $livewire->tableFilters['absence_date']['date'] ?? today();
                    
                    // Check if attendance exists for this date
                    $attendanceExists = $student->attendances()
                        ->whereDate('date', $date)
                        ->exists();
                        
                    if (!$attendanceExists) {
                        return 'gray'; // No attendance recorded yet
                    }
                    
                    return $student->isPresent($date) ? 'success' : 'danger';
                })
                ->label('حالة الحضور')
                ->state(function (Student $student, $livewire) {
                    $date = $livewire->tableFilters['absence_date']['date'] ?? today();
                    
                    // Check if attendance exists for this date
                    $attendanceExists = $student->attendances()
                        ->whereDate('date', $date)
                        ->exists();
                        
                    if (!$attendanceExists) {
                        return 'لم يتم تسجيل الحضور لليوم الذي تم اختياره';
                    }
                    
                    if( $student->isPresent($date))
                    {
                        return 'حاضر';
                    }
                    return $student->attendances()
                        ->whereDate('date', $date)->first()?->status == 'absent_with_excuse' ? 'غائب بعذر' : 'غائب بدون عذر';
                }),
                 Tables\Columns\TextColumn::make('notes')
                    ->limit(80)
                    ->state(function (Student $student, $livewire) {
                    $date = $livewire->tableFilters['absence_date']['date'] ?? today();
                    
                    return $student->attendances()
                        ->whereDate('date', $date)->first()?->notes;
                    })
                    ->label('ملاحظة'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('absence_date')
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label('تاريخ التصفية')
                            ->default(today())
                            ->maxDate(today())
                    ]),
                Filter::make('attendance_status')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                    'all' => 'الكل',
                                    'present' => 'حاضر',
                                    'absent_with_excuse' => 'غائب بعذر',
                                    'absent_without_excuse' => 'غائب بغير عذر',
                            ])
                            ->default('all')
                            ->disablePlaceholderSelection()
                    ])
                    ->query(function (Builder $query, array $data,$livewire) {
                      

                        $date = $livewire->tableFilters['absence_date']['date'] ?? today();
                        if($data['status'] == null)
                        {
                            return $query->whereDoesntHave('attendances', function ($q) use ($date) {
                                $q->whereDate('date', $date);
                            });
                        }
                        if($data['status'] == 'all')
                        {
                            return $query;
                        }
                        
                        return $query->whereHas('attendances', function ($q) use ($date,$data) {
                                $q->whereDate('date', $date)->where('status', $data['status']);
                            });
                        
                        
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('contactwhatsapp')
                    ->color('success')
                    ->icon('icon-whatsapp')
                    ->label(__('filament.candidate.actions.send_message'))
                    ->url(fn(Student $student)=>'https://wa.me/'.$student?->candidate?->phone)->openUrlInNewTab(),
                Tables\Actions\Action::make('attendance_change')
                      ->label(function (Student $student, $livewire) {
                            $date = $livewire->tableFilters['absence_date']['date'] ?? today();
                            $currentAttendance = Attendance::where('student_id', $student->id)
                                ->whereDate('date', $date)
                                ->first();
                            
                            // Dynamic label based on current status
                            if ($currentAttendance) {
                                return "تعديل الحضور ";
                            }
                            
                            return 'تسجيل الحضور';
                    })
                    ->icon('heroicon-o-check-circle')
                    ->color(function (Student $student, $livewire) {
                            $date = $livewire->tableFilters['absence_date']['date'] ?? today();
                            $currentAttendance = Attendance::where('student_id', $student->id)
                                ->whereDate('date', $date)
                                ->first();
                            
                            // Dynamic label based on current status
                            if ($currentAttendance) {
                                return 'success';
                            }
                            
                            return 'primary';
                    })
                    ->form(function (Student $student, $livewire) {
                        // Get current date from filters
                        $filters = [];
                        $date = $livewire->tableFilters['absence_date']['date'] ?? today();
                        
                        // Get current attendance record for this student and date
                        $currentAttendance = Attendance::where('student_id', $student->id)
                            ->whereDate('date', $date)
                            ->first();
                
                        return [
                            Forms\Components\Select::make('status')
                                ->label('الحالة')
                                ->options([
                                    'present' => 'حاضر',
                                    'absent_with_excuse' => 'غائب بعذر', // Fixed duplicate
                                    'absent_without_excuse' => 'غائب بغير عذر',
                                ])
                                ->required()
                                ->default($currentAttendance ? $currentAttendance->status : 'present'), // Pre-fill current status
                            
                            Forms\Components\Textarea::make('notes')
                                ->label('ملاحظات')
                                ->default($currentAttendance ? $currentAttendance->notes : ''), // Pre-fill current notes
                        ];
                    })
                    ->action(function (array $data, $record,$livewire) {
                       
                         $filters = []; 
                         $filters['date'] = $livewire->tableFilters['absence_date']['date'] ?? today()   ;
                        
                        $attendance = Attendance::updateOrCreate(
                            [
                                'student_id' => $record->id,
                                'date' => $filters['date'] ?? today(),
                            ],
                            [
                                'status' => $data['status'],
                                'notes' => $data['notes'],
                            ]
                        );
                    })
                    ->requiresConfirmation()
                    ->modalHeading('تعديل حالة الغياب')
                    ->modalDescription('الاستمرار في تغيير حالة الحضور')
                    ->modalSubmitActionLabel('استمرار')
                ,
            ])
            ->recordUrl(null)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulk_attendance')
                    ->label('تعديل الحضور الجماعي')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'present' => 'حاضر',
                                'absent_with_excuse' => 'غائب بعذر',
                                'absent_without_excuse' => 'غائب بغير عذر',
                            ])
                            ->required()
                            ->default('present'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات'),
                    ])
                    ->action(function (array $data, $livewire) {
                        $date = $livewire->tableFilters['absence_date']['date'] ?? today();
                        $selectedIds = $livewire->getSelectedTableRecords();
                        
                        if (empty($selectedIds)) {
                            Notification::make()
                                ->title('لم يتم اختيار أي طلاب')
                                ->warning()
                                ->send();
                            return;
                        }
                        
                        $total = count($selectedIds);
                        $updated = 0;
                        
                        
                        foreach ($selectedIds as $studentId) {
                            Attendance::updateOrCreate(
                                [
                                    'student_id' => $studentId?->id,
                                    'date' => $date,
                                ],
                                [
                                    'status' => $data['status'],
                                    'notes' => $data['notes'],
                                ]
                            );
                            $updated++;
                        }
                    
                        $livewire->deselectAllTableRecords();
                        
                        Notification::make()
                            ->title('تم الانتهاء')
                            ->body("تم تحديث الحضور لـ {$updated} من أصل {$total} طالب")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->modalHeading('تعديل الحضور الجماعي')
                    ->modalSubmitActionLabel('تأكيد التغيير')
                ]),
            ])
            ->headerActions([
                Action::make('generate_report')
                ->label('طباعة ')
                ->color('primary')
                ->action(function (array $data) {
                    return redirect()->route('attendance.pdf-download');
                }), 
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
