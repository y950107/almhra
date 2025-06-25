@extends('pdf.template')

@section('title','قائمة المترشحين')

@section('content')
    <table class="data">
        <thead>
        <tr>
            <th>#</th>
            <th>{{__('filament.candidate.fields.full_name')}}</th>
            <th>رقم الهوية</th>
            <th>{{__('filament.candidate.fields.email')}}</th>
            <th>{{__('filament.candidate.fields.phone')}}</th>
            <th>{{__('filament.candidate.fields.quran_level')}}</th>
            <th>{{__('filament.candidate.fields.tajweed_level')}}</th>
            <th>{{__('filament.candidate.fields.status')}}</th>
            <th>{{__('filament.candidate.fields.created_at')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($candidates as $index => $candidate)
      
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $candidate->full_name }}</td>
                <td>{{ $candidate->national_id }}</td>
                <td>{{ $candidate->email }}</td>
                <td>{{ $candidate->phone }}</td>
                <td>{{ __('filament.candidate.levels.' .$candidate->quran_level)}}</td>
                <td>{{ $candidate->self_evaluation }}</td>
                @php
                    $status = $candidate->status instanceof \App\Enums\CandidateStatus 
                    ? __('filament.candidate.status.' . $candidate->status->value) 
                    : 
                    __('filament.candidate.status.unknown')
                @endphp
                <td>{{ $status}}</td>
                <td>{{ date("d M Y",strtotime($candidate->created_at))}}</td>

            </tr>
        @endforeach
        </tbody>
    </table>
@endsection



