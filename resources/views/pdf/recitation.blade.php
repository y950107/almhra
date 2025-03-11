<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير حصص التسميع</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: right;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>تقرير حصص التسميع</h2>
<p>الفترة الزمنية: 
    {{ $timeRange === 'monthly' ? 'التقرير الشهري' : ($timeRange === 'yearly' ? 'التقرير السنوي' : 'تقرير مخصص من ' . $startDate . ' إلى ' . $endDate) }}
</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>التاريخ</th>
                <th>اسم الطالب</th>
                <th>المعلم</th>
                <th>عدد الصفحات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $index => $session)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $session->session_date }}</td>
                    <td>{{ $session->student->name ?? 'غير متوفر' }}</td>
                    <td>{{ $session->teacher->name ?? 'غير متوفر' }}</td>
                    <td>{{ $session->target_pages }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
