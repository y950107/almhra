@extends('pdf.template')

@section('title','تقرير الحضور والغياب للطلاب')

@section('content')
    <table class="data">
        <thead>
        <tr>
            <th>#</th>
            <th>اسم الطالب</th>
            <th>عدد الجلسات</th>
            <th>الحضور</th>
            <th>الغياب</th>
            <th>نسبة الحضور</th>
            <th>نسبة الغياب</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student->full_name }}</td>
                <td>{{ $student->total_sessions }}</td>
                <td>{{ $student->presence_count }}</td>
                <td>{{ $student->absence_count }}</td>
                <td>{{ $student->presence_percentage }}%</td>
                <td>{{ $student->absence_percentage }}%</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot class="tfoot">
        <tr>
            <th colspan="2">المجموع الكلي</th>
            <th>{{ $overallStats['total_sessions'] }}</th>
            <th>{{ $overallStats['total_presence'] }}</th>
            <th>{{ $overallStats['total_absence'] }}</th>
            <th>{{ $overallStats['total_presence_percentage'] }}%</th>
            <th>{{ $overallStats['total_absence_percentage'] }}%</th>
        </tr>
        </tfoot>
    </table>
@endsection

