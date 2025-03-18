<!DOCTYPE html>
<html lang="ar">

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
            /* توسيط الجدول داخل الـ div */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* إزالة المسافات بين الحدود */
        }

    

        /* إزالة الحدود الجانبية اليمنى واليسرى */
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


        th,
        td {
            border: 1px solid #000;
            text-align: center;
            padding: 0;
           
        }

        .data th {
            background-color: #53957b;
            color: white;
            font-weight: bold;
            font-size:medium;
        }

        td {
            
            font-weight: bold;
        }

        .absent {
            background-color: #f8d7da;
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


    <div class="row" style="background-color: #53957b;">
        <table >
            <thead>
                <tr>
                    <th>

                        <img style="align-content: space-around; width: 15%;"
                            src="{{ public_path('assets/images/logorapport/mahara.png') }}" alt="شعار المنشأة">

                    </th>
                    <th style="">
                        <div>
                            <h1>تقرير حصص التسميع</h1>
                        </div>
                        <div style="margin-top: 10%;">
                            <h2>
                                {{ $timeRange === 'monthly' ? 'التقرير الشهري' : ($timeRange === 'yearly' ? 'التقرير السنوي' : 'تقرير مخصص من ' . $startDate . ' إلى ' . $endDate) }}
                            </h2>
                        </div>
                        <br>
                        <div style="margin-top:30px;">
                            <p>{{ now()->format('Y-m-d') }}</p>
                        </div>




                    </th>
                    <th>
                        <img style="align-content: space-around; width: 15%;"
                            src="{{ public_path('assets/images/logorapport/mahara.png') }}" alt="شعار المنشأة">
                    </th>
                </tr>
            </thead>
        </table>
    </div>


    @php
        // تحميل ملف JSON وتحويله إلى مصفوفة
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
                <th>معلم الحلقة</th>
                <th>الجلسة</th>
                <th>القراءة</th>
                <th>حالة الحضور</th>
                <th>السورة (من - إلى)</th>
                <th>الآيات (من - إلى)</th>
                <th>المحقق</th>
                <th>المستهدف</th>
                <th>نسبة الإنجاز</th>
                <th>المحقق التراكمي</th>
                <th>المستهدف التراكمي</th>
                <th>نسبة الانجاز</th>
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
                    <td>{{ $session->present_status ? 'حاضر' : 'غائب' }}</td>
                    <td>
                        {{ $quranService->getSurahName($session->start_surah_id) }} -
                        {{ $quranService->getSurahName($session->end_surah_id) }}
                    </td>
                    <td>{{ $session->start_ayah_id }} - {{ $session->end_ayah_id }}</td>
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

            <tr>
                <th>#</th>
                <th> الطالب</th>
                <th>معلم الحلقة</th>
                <th>الجلسة</th>
                <th>القراءة</th>
                <th>حالة الحضور</th>
                <th>السورة (من - إلى)</th>
                <th>الآيات (من - إلى)</th>
                <th>المحقق</th>
                <th>المستهدف</th>
                <th>نسبة الإنجاز</th>
                <th>المحقق التراكمي</th>
                <th>المستهدف التراكمي</th>
                <th>نسبة الانجاز</th>

            </tr>
        </tbody>
    </table>

    <div class="footer">
        {{-- <p>تم إصدار التقرير بواسطة: {{ $generated_by }}</p> --}}
        {{-- <p>تاريخ الإصدار: {{ $generated_at }}</p> --}}
    </div>
</body>

</html>
