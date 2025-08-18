@extends('pdf.template')

@section('title','قائمة مقابلات التقييم المختصرة')

@section('content')
    {{-- write from and to date  --}}
    
    <p>من {{ $from_date ?? 'غير محدد' }} إلى {{ $to_date ?? 'غير محدد' }}</p>
    <table class="data">
        <thead>
        <tr>
            <th>#</th>
            <th>المترشح</th>
            <th>{{__('filament.candidate.fields.phone')}}</th>
            <th>درجة التجويد</th>
            <th>درجة الصوت</th>
            <th>درجة الحفظ</th>
            <th>الملاحظات</th>
        </tr>
        </thead>
        <tbody>
        @foreach($evaluations as $index => $evaluation)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $evaluation->candidate->full_name }}</td>
                <td>{{ $evaluation->candidate->phone }}</td>
                <td>{{ $evaluation->tajweed_score }}</td>
                <td>{{ $evaluation->voice_score }}</td>
                <td>{{ $evaluation->memorization_score }}</td>
                <td>{{ $evaluation->notes }}</td>

            </tr>
        @endforeach
        </tbody>
    </table>
@endsection



