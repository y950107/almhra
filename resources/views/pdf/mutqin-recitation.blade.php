@extends('pdf.template')

@section('title',$title)

@section('content')
    <table class="data">
        <thead>
            <tr>
                <th>#</th>
                <th>الطالب</th>
                <th>معلم الحلقة</th>
                <th>التسجيل</th>
                <th>الغياب</th>
                <th>من سورة</th>
                <th>آية</th>
                <th>إلى سورة</th>
                <th>آية</th>
                <th>المحقق</th>
                <th>المستهدف</th>
                <th>نسبة الإنجاز</th>

                <th>من سورة</th>
                <th>آية</th>
                <th>إلى سورة</th>
                <th>آية</th>
                <th>المحقق</th>
                <th>المستهدف</th>
                <th>نسبة الإنجاز</th>
                <th>معدل التقييم</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($statsPerStudent as $index => $stat)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $stat['student_name'] }}</td>
                    <td>{{ $stat['teacher_name'] }}</td>
                    <td>{{ $stat['registration_month'] }}</td>
                    <td>{{ $stat['absences'] }}</td>


                    <td>{{ $stat['mem_start_surah_name'] }}</td>
                    <td>{{ $stat['mem_start_ayah_id'] }}</td>
                    <td>{{ $stat['mem_end_surah_name'] }}</td>
                    <td>{{ $stat['mem_end_ayah_id'] }}</td>
                    <td>{{ $stat['mem_pages_read'] }}</td>
                    <td>{{ $stat['mem_monthly_target'] }}</td>
                    <td>{{ $stat['mem_monthly_percentage'] }}%</td>

                    <td>{{ $stat['rev_start_surah_name'] }}</td>
                    <td>{{ $stat['rev_start_ayah_id'] }}</td>
                    <td>{{ $stat['rev_end_surah_name'] }}</td>
                    <td>{{ $stat['rev_end_ayah_id'] }}</td>
                    <td>{{ $stat['rev_pages_read'] }}</td>
                    <td>{{ $stat['rev_monthly_target'] }}</td>
                    <td>{{ round(($stat['rev_pages_read'] / ($stat['rev_monthly_percentage'] == 0 ? 1 : $stat['rev_monthly_target'])) * 100 )}}%</td>
                    {{-- <td>{{ $stat['rev_monthly_percentage'] }}%</td> --}}
                    <td>{{ $stat['avg_evaluation_score'] }}</td>
                </tr>
            @endforeach


        </tbody>
        <tfoot class="tfoot" style="background-color: rgb(28, 22, 35)">

            <tr>
                <th colspan="4" rowspan="2">مؤشر الأداء لجميع الحلقات</th>
                <th>{{ $overallStats['total_absences_percentage'] }}%</th>
                <th colspan="4" rowspan="2">الحفظ</th>
                <th>{{ $overallStats['mem_total_pages'] }}</th>
                <th>{{ $overallStats['mem_total_monthly_target'] }}</th>
                <th>{{ $overallStats['mem_total_monthly_percentage'] }}%</th>
                <th colspan="4" rowspan="2">المراجعة</th>
                <th>{{ $overallStats['rev_total_pages'] }}</th>
                <th>{{ $overallStats['rev_total_monthly_target'] }}</th>
                <th>{{ $overallStats['rev_total_monthly_percentage'] }}%</th>
                <th>{{ $overallStats['total_score'] }}</th>
            </tr>
            <tr>

                <th>الغياب</th>

                <th colspan="2">عدد الاوجه</th>

                <th>نسبة الانجاز</th>
                <th>معدل التقييم</th>
                <th colspan="2">عدد الاوجه</th>

                <th>المؤشر العام</th>

            </tr>
        </tfoot>
    </table>
@endsection
