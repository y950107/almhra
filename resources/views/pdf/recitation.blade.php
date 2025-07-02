@extends('pdf.template')


@section('title',$title )

@section('content')
    <table class="data">
        <thead>
        <tr>
            <th>#</th>
            <th>الطالب</th>
            <th>معلم الحلقة</th>
            <th>التسجيل</th>
            <th>من سورة</th>
            <th>آية</th>
            <th>إلى سورة</th>
            <th>آية</th>
            <th>الغياب</th>
            <th>المحقق</th>
            <th>المستهدف</th>
            <th>نسبة الإنجاز</th>
            @if($timeRange === 'monthly')
                <th>المحقق التراكمي</th>
                <th>المستهدف التراكمي</th>
                <th>نسبة الإنجاز</th>
            @endif
            <th>معدل التقييم</th>
            <th>المتن</th>
            <th>حفظ المتن</th>

        </tr>
        </thead>
        <tbody>
            @php
                $mem_lines_count=0;
            @endphp
        @foreach ($statsPerStudent as $index => $stat)
        @php
            $monthsBetween =  round( \Carbon\Carbon::createFromDate($stat['student_start_date'])->diffInMonths(now()),2) ;
            // $monthsBetween = $monthsBetween < 1 ? 1 : $monthsBetween;
            $target = request()['time_range'] =="monthly" ? $stat['monthly_target'] : $stat['monthly_target'] * $monthsBetween;
            $mem_lines_count+=$stat['mem_lines'];
        @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $stat['student_name'] }}</td>
                <td>{{ $stat['teacher_name'] }}</td>
                <td>{{ $stat['registration_month'] }}</td>
                <td>{{ $stat['start_surah_name'] }}</td>
                <td>{{ $stat['start_ayah_id'] }}</td>
                <td>{{ $stat['end_surah_name'] }}</td>
                <td>{{ $stat['end_ayah_id'] }}</td>
                <td>{{ $stat['absences'] }}</td>
                <td>{{ $stat['pages_read'] }}</td>
                <td>{{ $target }}</td>
                 <td>{{ round(($stat['pages_read'] / ($target  == 0 ? 1 : $target) * 100)) }}%</td>
                <!--<td>{{ $stat['monthly_percentage'] }}%</td>-->
                @if($timeRange === 'monthly')
                    <td>{{ $stat['cumulative_pages'] }}</td>
                    {{-- <td>{{ $monthly_target}}</td> --}}
                    <td>{{ $stat['cumulative_target'] }}</td>
                    <td>{{ $stat['cumulative_percentage'] }}%</td>
                @endif
                <td>{{ $stat['avg_evaluation_score'] }}</td>
                <td>{{ $stat['translated_lesson_title'] }}</td>
                <td>{{ $stat['mem_lines'] }}</td>
            </tr>
        @endforeach

        </tbody>

        <tfoot class="tfoot" style="background-color: rgb(28, 22, 35)">

        <tr>
            <th colspan="8" rowspan="2">مؤشر الأداء لجميع الحلقات</th>
            <th>{{ $overallStats['total_absences_percentage'] }}%</th>
            <th>{{ $overallStats['total_pages'] }}</th>
            <th>{{ $overallStats['total_monthly_target'] }}</th>
            <th>{{ $overallStats['total_monthly_percentage'] }}%</th>
            @if($timeRange === 'monthly')
                <th>{{ $overallStats['total_cumulative_pages'] }}</th>
                <th>{{ $overallStats['total_cumulative_target'] }}</th>
                <th>{{ $overallStats['total_cumulative_percentage'] }}%</th>
            @endif
            <th colspan="">{{ $overallStats['total_score'] }}</th>
            <th colspan=""></th>
            <th colspan="">{{$mem_lines_count}}</th>
        </tr>
        <tr>

            <th>الغياب</th>
            <th colspan="2">عدد الاوجه</th>

            <th>نسبة الانجاز</th>
            <th>معدل التقييم</th>
            @if($timeRange === 'monthly')
                <th colspan="2">عدد الاوجه</th>

                <th colspan="3">المؤشر العام</th>
            @else 
            <th colspan=""></th>
            <th colspan=""></th>
            @endif
            
        </tr>
        </tfoot>


    </table>
@endsection

