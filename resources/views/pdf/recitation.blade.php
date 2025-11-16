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
            @if($is_mahir)
            <th>المتن</th>
            <th>حفظ المتن</th>
            @endif
        </tr>
        </thead>
        <tbody>
            @php
                $mem_lines_count=$cumulative_percentage_count=0;
                $achievement_target_sum=0;
                $total_records = count($statsPerStudent) > 0 ? count($statsPerStudent) : 1;
            @endphp
        @foreach ($statsPerStudent as $index => $stat)
        @php
       
            $end_date  =request('end_date') ?? now();
            if($stat['program_end_date'] && \Carbon\Carbon::createFromDate($stat['student_start_date'])->lessThan(now())) $end_date = $stat['program_end_date'];
            
            
            $end_date = \Carbon\Carbon::parse($end_date);
            $monthsBetween =  round( \Carbon\Carbon::createFromDate($stat['student_start_date'])->diffInMonths($end_date),2) ;
            // $monthsBetween = $monthsBetween < 1 ? 1 : $monthsBetween;
            $target = request()['time_range'] =="monthly" ? $stat['monthly_target'] : $stat['monthly_target'] * $monthsBetween;
            $mem_lines_count+=intval($stat['mem_lines']);
            $achi_percent=round(($stat['pages_read'] / ($target  == 0 ? 1 : $target) * 100));
            $achievement_target_sum+=$achi_percent;
            
            //total of cumulative_percentage
            $cumulative_percentage_count+=floatval($stat['cumulative_percentage']);
            
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
                 <td>{{ $achi_percent }}%</td>
                <!--<td>{{ $stat['monthly_percentage'] }}%</td>-->
                @if($timeRange === 'monthly')
                    <td>{{ $stat['cumulative_pages'] }}</td>
                    {{-- <td>{{ $monthly_target}}</td> --}}
                    <td>{{ $stat['cumulative_target'] }}</td>
                    <td>{{ $stat['cumulative_percentage'] }}%</td>
                @endif
                <td>{{ $stat['avg_evaluation_score'] }}</td>
                @if($is_mahir)
                <td>{{ $stat['translated_lesson_title'] }}</td>
                <td>{{ $stat['last_lesson_total_mem_lines']}}</td>
                @endif
            </tr>
        @endforeach
        </tbody>

        <tfoot class="tfoot" style="background-color: rgb(28, 22, 35)">
        @php
        $month_name = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر'
        ];
        @endphp
        <tr>
            <th colspan="8" rowspan="2">
                مؤشر الأداء لجميع الحلقات 
                @if(request('month'))
                    لشهر {{ $month_name[request('month')] }} {{ date('Y')}}
                @endif
            </th>
            <th>{{ $overallStats['total_absences_percentage'] }}%</th>
            <th>{{ $overallStats['total_pages'] }}</th>
            <th>{{ $overallStats['total_monthly_target'] }}</th>
            <!--<th>{{ round($achievement_target_sum / $total_records) }}%</th>-->
            <th>{{ round($achievement_target_sum / $total_records) }}%</th>
            @if($timeRange === 'monthly')
                <th>{{ $overallStats['total_cumulative_pages'] }}</th>
                <th>{{ $overallStats['total_cumulative_target'] }}</th>
                <!--<th>{{ round($cumulative_percentage_count/$total_records)}}%</th>-->
                <!--<th>{{ round($achievement_target_sum / $total_records) }}%</th>-->
                <th>{{ $overallStats['total_cumulative_percentage'] }}%</th>
            @endif
            <th colspan="">{{ $overallStats['total_score'] }}</th>
            @if($is_mahir)
            <th colspan=""></th>
            <th colspan="">{{$mem_lines_count}}</th>
            @endif
        </tr>
        <tr>

            <th>الغياب</th>
            <th colspan="2">عدد الاوجه</th>

            <th>نسبة الانجاز</th>
            
            @if($timeRange === 'monthly')
                <th colspan="2">عدد الاوجه</th>

                <th colspan="">المؤشر العام</th>
                <th>معدل التقييم</th>
                @if($is_mahir)

                <th colspan=""></th>
                <th colspan=""></th>
                @endif
            @else 
            @if($is_mahir)
            <th colspan=""></th>
            <th colspan=""></th>
            @endif
            <th>معدل التقييم</th>
            @endif
            
        </tr>
        </tfoot>


    </table>
@endsection

