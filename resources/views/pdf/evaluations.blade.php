@extends('pdf.template')

@section('title','قائمة مقابلات التقييم')

@section('content')
    <table class="data">
        <thead>
        <tr>
            <th>#</th>
            <th>المترشح</th>
            <th>{{__('filament.candidate.fields.email')}}</th>
            <th>{{__('filament.candidate.fields.program')}}</th>
            <th>المقيم</th>
            <th>درجة التجويد</th>
            <th>درجة الصوت</th>
            <th>درجة الحفظ</th>
            <th>{{__('filament.candidate.fields.status')}}</th>
            <th>المعدل</th>
            <th>{{__('filament.candidate.fields.created_at')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($evaluations as $index => $evaluation)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $evaluation->candidate->full_name }}</td>
                <td>{{ $evaluation->candidate->email }}</td>
                <td>{{ \App\Models\Candidate::getProgramTypes()[$evaluation->candidate->program_type] ?? 'غير معروف' }}</td>
                <td>{{ $evaluation->evaluator->name }}</td>
                <td>{{ $evaluation->tajweed_score }}</td>
                <td>{{ $evaluation->voice_score }}</td>
                <td>{{ $evaluation->memorization_score }}</td>
                @php
                    //  $status = $evaluation->status instanceof \App\Enums\EvaluationStatus ? __('filament.candidate.status.' . $evaluation->self_evaluation) : __('filament.candidate.status.unknown')
                     $status = $evaluation->status instanceof \App\Enums\EvaluationStatus
                                ?  __('filament.candidate.status.' . $evaluation->status->value)
                                : __('filament.evaluation.status.unknown');
                @endphp
                <td>{{ $status}}</td>
                <td>{{ $evaluation->total_score. '%' }}</td>
                <td>{{ date("d M Y",strtotime($evaluation->created_at))}}</td>

            </tr>
        @endforeach
        </tbody>
    </table>
@endsection



