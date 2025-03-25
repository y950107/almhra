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

    @php
    use App\Settings\GeneralSettings;
    $logo= app(GeneralSettings::class)->logo;
    //dd($logo);
    @endphp
    <div class="row">
        <table>
            <thead>
                <tr>
                    <th style="text-align: right;">

                        <img style="align-content: space-around; width: 15%;"
                        src="data:image/png;base64,{{ base64_encode(file_get_contents( url($logo)) ) }}" alt="شعار المنشأة">

                    </th>
                    <th style="">
                        <div>
                            <h1>جامع والدة الأمير بندر بن عبدالعزيز-بحي الندى</h1>
                        </div>
                        <br>
                        <div>
                            <h2 style="margin-top: 12%;">
                                {{ $timeRange === 'monthly' ? 'التقرير الشهري' : ($timeRange === 'yearly' ? 'التقرير السنوي' : 'تقرير مخصص من ' . $startDate . ' إلى ' . $endDate) }}
                            </h2>
                        </div>
                        <br>
                        <div style="margin-top:30px;">
                            
                        </div>




                    </th>
                    <th style="text-align: left;">
                        <img style="align-content: space-around; width: 15%;"
                            src="{{ public_path('assets/images/logorapport/mahara.png') }}" alt="شعار المنشأة">
                    </th>
                </tr>
            </thead>
        </table>
    </div>


    @php
        
        use App\Services\Moshaf_madina_Service;
        $quranService = new Moshaf_madina_Service();

        $cumulativeData = $sessions->groupBy('student_id')->map(function ($group) {
            return $group->sum('target_pages');
        });
        $cumulativeData2 = $sessions->groupBy('student_id')->map(function ($group) {
            return $group->sum('actual_pages');
        });



        // إنشاء قائمة السور بحيث يمكن الوصول إليها بسهولة
        // $suras = collect($quranData)->keyBy('sura_no');

    @endphp
    <table class="data">
        <thead>
            <tr>
                <th>#</th>
                <th> الطالب</th>
                <th> المشرف</th>
                <th>الجلسة</th>
                <th>القراءة</th>
                <th>السورة (من - إلى)</th>
                <th>الآيات (من - إلى)</th>
                <th>الحضور</th>
                <th>المحقق</th>
                <th>المستهدف</th>
                <th>نسبة الإنجاز</th>
                <th>المحقق التراكمي</th>
                <th>المستهدف التراكمي</th>
                <th>نسبةالانجاز</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sessions as $index => $session)
                @php
                    $studentId = $session->student->id;
                    $totalTargetPages = $cumulativeData[$studentId] ?? 0;
                    $totalActualtPages = $cumulativeData2[$studentId] ?? 0;
                @endphp
                <tr class="{{ $session->present_status ? '' : 'absent' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $session->student->candidate->full_name }}</td>
                    <td>{{ $session->student->teacher->name }}</td>
                    <td>{{ $session->student->creates_at }}</td>
                    <td>{{ $session->student->candidate->desired_recitations[1] }}</td>
                    <td>
                        {{ $quranService->getSurahName($session->start_surah_id) }} -
                        {{ $quranService->getSurahName($session->end_surah_id) }}
                    </td>
                    <td>{{ $session->start_ayah_id }} - {{ $session->end_ayah_id }}</td>
                    <td>{{ $session->present_status ? 'حاضر' : 'غائب' }}</td>
                    <td>{{ $session->actual_pages }}</td>
                    <td>{{ $session->target_pages }}</td>
                    <td>{{ $session->achievement_percentage }}%</td>

                    <td>{{ $totalActualtPages }}</td>
                    <td>{{ $totalTargetPages }}</td>
                    <td>
                        @if ($totalTargetPages > 0)
                            {{ round(($totalActualtPages / $totalTargetPages) * 100, 2) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
            @endforeach


        </tbody>
        <tfoot class="tfoot" style="background-color: rgb(28, 22, 35)">

            <tr>
                <th colspan="7" rowspan="2">مؤشر الأداء لجميع الحلقات لشهر: {{ $currentMonth }}</th>

                <th></th>
                <th>{{$sumActualPages }}</th>
                <th>{{$sumTargetPages}}</th>
                <th> {{ sprintf('%.2f%%', ($sumActualPages/$sumTargetPages)*100)}}</th>
                <th>
               {{$sumToactualPages}}
                </th>
                <th>{{$sumTotalTargetPages}}</th>
                <th>{{ sprintf('%.2f%%', ($sumToactualPages/$sumTotalTargetPages)*100)}}</th>

            </tr>
            <tr>

                <th>الغياب</th>
                <th colspan="2">عدد الاوجه</th>

                <th>نسبة الانجاز</th>
                <th colspan="2">عدد الاوجه المنجزة </th>

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
