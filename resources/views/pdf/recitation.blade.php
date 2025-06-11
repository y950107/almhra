<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير حصص التسميع</title>
    <style>
        body {
            font-family: 'Amiri', sans-serif;
            direction: rtl;
            text-align: right;
            background-image: url('{{ public_path('assets/images/background/3.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .row {
            display: flex;
            justify-content: center;
            margin-bottom: 5%;

        }

        table {
            width: 100%;
            border-collapse: collapse;

        }




        .row th {
            border-style: none;

        }



        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            flex-grow: 1;
        }

        .title h1 {
            margin: 0;
        }

        .title h2 {
            font-size: 18px;
            margin: 5px 0;
        }

        .title p {
            font-size: 14px;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }


        .data th,
        td {
            border: 1px solid #000;
            padding: 5px 10px;
            white-space: nowrap;
            text-align: center;

        }

        .data th {
            background-color: #53957b;
            color: white;
            font-size: large;


        }

        td {

            font-size: large;
        }

        .absent {
            background-color: #f8d7da;
        }

        .tfoot th {
            background-color: chocolate;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            text-align: center;
        }
    </style>





</head>

<body>

    <section class="">

    </section>


    <div class="row">
        <table>
            <thead>
                <tr>
                    <th style="text-align: right;">

                        <img style="align-content: space-around; width: 15%;"
                            src="{{ public_path('assets/logo.png') }}" alt="شعار المنشأة">

                    </th>
                    <th style="">
                        <div>
                            <h1>جامع والدة الأمير بندر بن عبدالعزيز-بحي الندى</h1>
                        </div>
                        <br>
                        <div>
                            <h2 style="margin-top: 12%;">
                               {{ $timeRange === 'monthly' ? 'التقرير الشهري' : ($timeRange === 'yearly' ? 'التقرير السنوي' : 'تقرير مخصص من ' . $startDate . ' إلى ' . $endDate) }} - {{ $program_name ?? '' }}
                            </h2>
                        </div>
                        <br>
                        <div style="margin-top:30px;">

                        </div>




                    </th>
                    <th style="text-align: left;">
                        <img style="align-content: space-around; width: 15%;"
                            src="{{ public_path('assets/favicon.png') }}" alt="شعار المنشأة">
                    </th>
                </tr>
            </thead>
        </table>
    </div>


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
                <th>المحقق التراكمي</th>
                <th>المستهدف التراكمي</th>
                <th>نسبة الإنجاز</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($statsPerStudent as $index => $stat)
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
                    <td>{{ $stat['monthly_target'] }}</td>
                    <td>{{ $stat['monthly_percentage'] }}%</td>
                    <td>{{ $stat['cumulative_pages'] }}</td>
                    <td>{{ $stat['cumulative_target'] }}</td>
                    <td>{{ $stat['cumulative_percentage'] }}%</td>
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
                <th>{{ $overallStats['total_cumulative_pages'] }}</th>
                <th>{{ $overallStats['total_cumulative_target'] }}</th>
                <th>{{ $overallStats['total_cumulative_percentage'] }}%</th>

            </tr>
            <tr>

                <th>الغياب</th>
                <th colspan="2">عدد الاوجه</th>

                <th>نسبة الانجاز</th>
                <th colspan="2">عدد الاوجه</th>

                <th>المؤشر العام</th>

            </tr>
        </tfoot>
    </table>

    <div class="footer">
        {{-- <p>تم إصدار التقرير بواسطة: {{ $generated_by }}</p> --}}
        {{-- <p>تاريخ الإصدار: {{ $generated_at }}</p> --}}
    </div>
</body>

</html>
