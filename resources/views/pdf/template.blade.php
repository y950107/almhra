<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'تقرير')</title>
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
                    <h1>مقرأة المهرة بجامع والدة الأمير بندر بن عبدالعزيز - حي الندى</h1>
                </div>
                <br>
                <div>
                    <h2 style="margin-top: 12%;">
                        @yield('title', 'تقرير')
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


@yield('content')

<div class="footer">
    {{-- <p>تم إصدار التقرير بواسطة: {{ $generated_by }}</p> --}}
    {{-- <p>تاريخ الإصدار: {{ $generated_at }}</p> --}}
</div>
</body>

</html>
