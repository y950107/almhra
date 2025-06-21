@extends('pdf.template')

@section('title','تقرير الطلاب')

@section('content')
    <table class="data">
        <thead>
        <tr>
            <th>#</th>
            <th>اسم الطالب</th>
            <th>البريد الإلكتروني</th>
            <th>رقم الهاتف</th>
            <th>البرنامج</th>
            <th>تاريخ التسجيل</th>
            <th>نسبة التسميع الحضوري</th>
            <th>نسبة التسميع عن بعد</th>
            <th>نسبة الانجاز</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student->full_name }}</td>
                <td>{{ $student->user->email }}</td>
                <td>{{ $student->user->phone }}</td>
                <td>{{ $student->program_type }}</td>
                <td>{{ \Carbon\Carbon::parse($student->start_date)->format('d-m-Y') }}</td>
                <td>{{ $student?->present_sessions_percentage }}%</td>
                <td>{{ $student?->online_sessions_percentage }}%</td>
                <td>{{ $student->progress_percentage }}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

