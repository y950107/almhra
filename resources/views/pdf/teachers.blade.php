@extends('pdf.template')

@section('title','قائمة المعلمين')

@section('content')
    <table class="data">
        <thead>
        <tr>
            <th>#</th>
            <th>الاسم</th>
            <th>البريد الإلكتروني</th>
            <th>رقم الهاتف</th>
        </tr>
        </thead>
        <tbody>
        @foreach($teachers as $index => $teacher)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $teacher->name }}</td>
                <td>{{ $teacher->email }}</td>
                <td>{{ $teacher->phone }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection



