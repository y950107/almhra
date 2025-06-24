@extends('pdf.template')

@section('title','تقرير المفصل لبرنامج التأسيس')

@section('content')
    <table class="data">
        <thead>
            <tr>
                <th>#</th>
                <th>تاريح الجلسة</th>
                <th>الطالب</th>
                <th>معلم الحلقة</th>
                <th>الغياب</th>
                <th>التقييم</th>
                <th>المتن</th>
                <th>حفظ المتن</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($sessions as $index => $session)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $session->session_date }}</td>
                    <td>{{ $session?->student?->candidate?->full_name }}</td>
                    <td>{{ $session?->student?->teacher?->name }}</td>
                    <td>{{ $session?->translated_present }}</td>
                    <td>{{ $session->evaluationScore }}</td>
                    <td>{{ $session->almaherRecitation?->translated_lesson_title }}</td>
                    <td>{{ $session->almaherRecitation?->mem_lines }}</td>
                </tr>
            @endforeach


        </tbody>
    </table>
@endsection
