@extends('pdf.template')

@section('title','قائمة الطلاب المتخرجين')

@section('content')
    <table class="data">
        <thead>
        <tr>
            <th>#</th>
            <th>اسم الطالب</th>
            <th>رقم الهاتف</th>
            <th>الشيح</th>
            <th>البرنامج</th>
            <th>تاريخ التسجيل</th>
            <th>القراءة / الرواية</th>
            {{-- <th>طريقة التسميع </th> --}}
            <th>التقييم العام</th>
        </tr>
        </thead>
        <tbody>
        @if($students)
            @foreach ($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student->full_name }}</td>
                <td>{{ $student->user->phone }}</td>
                <td>{{ $student->teacher->name }}</td>
                <td>{{ $student->program_type }}</td>
                <td>{{ $student->currentHalakas()->latest()->first()->attend_at ?? \Carbon\Carbon::parse($student->start_date)->format('d-m-Y') }}</td>
                <td>{{ $student?->recitationSessions()->where('present','present')
                ->when($student->currentHalakas()->latest()->first(), function ($query) use ($student) {
                    $query->whereHalakaId($student->currentHalakas()->latest()->first()?->halaka_id);
                })
                ->first()?->recitation_narration }}</td>
                {{-- <td>{{ $student?->recitationSessions()->where('present','present')->whereHalakaId($student->currentHalakas()->latest()->first()->id)?->recitation_type }}%</td> --}}
                <td>{{ $student->progress_percentage }}%</td>
            </tr>
             @endforeach
        @endif 
        </tbody>
    </table>
@endsection

