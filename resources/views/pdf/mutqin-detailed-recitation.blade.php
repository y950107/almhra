@extends('pdf.template')

@section('title','تقرير المفصل لبرنامج المتقن')

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
                <th>الدرس المصاحب</th>
                <th>عنوان الدرس</th>

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
                    <td>{{ $session->almutqinRecitation?->translated_lesson_type }}</td>
                    <td>{{ $session->almutqinRecitation?->lesson_title }}</td>
                </tr>
            @endforeach


        </tbody>
    </table>
@endsection
