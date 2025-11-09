@extends('pdf.template')

@section('title', 'تقرير الحضور والغياب للطلاب')

@section('content')
    <h2>تاريخ التقرير: {{ $filters['attendance_date'] ?? $filters['attendance_date'] ?? now()->format('Y-m-d') }}</h2>
    @if($filters['status'] && $filters['status'] != 'الكل')<h2>حالة الحضور : {{ $attendance_date ?? $filters['status'] ?? now()->format('Y-m-d') }}</h2> @endif
    
    <table class="data">
        <thead>
        <tr>
            <th>#</th>
            <th>اسم الطالب</th>
            <th>المجموعة</th>
            <th>حالة الحضور</th>
            <th>ملاحظة</th>
        </tr>
        </thead>
        <tbody>
           
        @foreach ($students as $index => $studentData)
            @php
                $student = $studentData['student'] ?? $studentData;
                $status_text = $studentData['status_text'] ?? 'لم يسجل';
                $notes = $studentData['notes'] ?? '-';
                $halaka = $student->halakas()->latest()->first();
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student->name ?? $student->full_name }}</td>
                <td>{{ $halaka->name ?? '-' }}</td>
                <td>{{ $status_text }}</td>
                <td>{{ $notes }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot class="tfoot">
        <tr>
            @php
                $totalStudents = count($students);
                $presentCount = collect($students)->where('status', 'present')->count();
                $absentCount = collect($students)->whereIn('status', ['absent_with_excuse', 'absent_without_excuse'])->count();
                $notRecordedCount = $totalStudents - $presentCount - $absentCount;
                $attendanceRate = $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100, 2) : 0;
            @endphp
            <th colspan="3">الإجمالي</th>
            <th>
                حاضر: {{ $presentCount }}<br>
                غائب: {{ $absentCount }}<br>
                لم يسجل: {{ $notRecordedCount }}
            </th>
            <th>نسبة الحضور: {{ $attendanceRate }}%</th>
        </tr>
        </tfoot>
    </table>
@endsection

