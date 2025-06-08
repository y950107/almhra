<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الحضور والغياب</title>
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

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .data th, .data td {
            border: 1px solid #000;
            padding: 5px 10px;
            text-align: center;
            font-size: large;
        }

        .data th {
            background-color: #53957b;
            color: white;
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

<div class="row">
    <table>
        <thead>
        <tr>
            <th style="text-align: right;">
                <img style="width: 15%;" src="{{ public_path('assets/logo.png') }}" alt="شعار المنشأة">
            </th>
            <th>
                <h1>جامع والدة الأمير بندر بن عبدالعزيز-بحي الندى</h1>
                <h2>تقرير الحضور والغياب للطلاب</h2>
            </th>
            <th style="text-align: left;">
                <img style="width: 15%;" src="{{ public_path('assets/favicon.png') }}" alt="شعار المنشأة">
            </th>
        </tr>
        </thead>
    </table>
</div>

<table class="data">
    <thead>
    <tr>
        <th>#</th>
        <th>اسم الطالب</th>
        <th>عدد الجلسات</th>
        <th>الحضور</th>
        <th>الغياب</th>
        <th>نسبة الحضور</th>
        <th>نسبة الغياب</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($students as $index => $student)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $student->full_name }}</td>
            <td>{{ $student->total_sessions }}</td>
            <td>{{ $student->presence_count }}</td>
            <td>{{ $student->absence_count }}</td>
            <td>{{ $student->presence_percentage }}%</td>
            <td>{{ $student->absence_percentage }}%</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot class="tfoot">
    <tr>
        <th colspan="2">المجموع الكلي</th>
        <th>{{ $overallStats['total_sessions'] }}</th>
        <th>{{ $overallStats['total_presence'] }}</th>
        <th>{{ $overallStats['total_absence'] }}</th>
        <th>{{ $overallStats['total_presence_percentage'] }}%</th>
        <th>{{ $overallStats['total_absence_percentage'] }}%</th>
    </tr>
    </tfoot>
</table>

{{--<div class="footer">
    <p>تم إصدار التقرير تلقائيًا</p>
</div>--}}
</body>
</html>
