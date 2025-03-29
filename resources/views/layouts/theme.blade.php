<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }} " dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    @if(app()->getLocale() == 'ar') dir="rtl"  @else dir="ltr" @endif>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    @if(app()->getLocale() == 'ar')
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css" integrity="sha384-JvExCACAZcHNJEc7156QaHXTnQL3hQBixvj5RV5buE7vgnNEzzskDtx9NQ4p6BJe" crossorigin="anonymous">
    @else 
    <link href="{{asset('assets/css/bootstrap.css')}}" rel="stylesheet">
    @endif
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/responsive.css')}}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    
    <meta name="description" content="مقرأة المهرة  - جامع والدة الأمير بندر بن عبد العزيز">
    <meta name="keywords" content="مقرأة المهرة,قرءان كريم,تحفيظ القرءان,القرءان,جامع والدى الأمير بندر بن عبد العزيز,جلسات التسميع للقرءان,علوم التجويد">
    <meta name="author" content="مقرأة المهرة  - جامع والدة الأمير بندر بن عبد العزيز">
    <meta name="website" content="https://almhrah.com">
    <meta name="email" content="contact@almhrah.com">
    <meta name="version" content="2.0.0">

    <meta itemprop="name" content="مقرأة المهرة  - جامع والدة الأمير بندر بن عبد العزيز">
    <meta itemprop="description" content="{{ app(\App\Settings\GeneralSettings::class)->company_name }}">
    <meta itemprop="image" content="{{asset("assets/".app(\App\Settings\GeneralSettings::class)->logo)}}">

    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ app(\App\Settings\GeneralSettings::class)->company_name }}">
    <meta name="twitter:description" content="{{ app(\App\Settings\GeneralSettings::class)->company_name }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{asset("assets/".app(\App\Settings\GeneralSettings::class)->logo)}}">

    <meta property="og:title" content="{{ app(\App\Settings\GeneralSettings::class)->company_name }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://www.almhrah.com" />
    <meta property="og:image" content="{{asset("assets/".app(\App\Settings\GeneralSettings::class)->logo)}}" />
    <meta property="og:description" content="{{ app(\App\Settings\GeneralSettings::class)->company_name }}" />
    <meta property="og:site_name" content="{{ app(\App\Settings\GeneralSettings::class)->company_name }}" />
    <meta property="fb:app_id" content>
    <!-- favicon -->
    <link href="{{ app(\App\Settings\GeneralSettings::class)->favicon ? asset("storage/".app(\App\Settings\GeneralSettings::class)->favicon) : asset('favicon.ico')}}" rel="shortcut icon">
    <link rel="icon" href="{{ app(\App\Settings\GeneralSettings::class)->favicon ? asset("storage/".app(\App\Settings\GeneralSettings::class)->favicon) : asset('favicon.ico')}}">

    <!-- bing registration service-->
    <meta name="msvalidate.01" content="BDFBD7C7EF3727604D37A7FFE4A7649F" />
    <!-- Css -->
    <link href="{{asset('assets/libs/tobii/css/tobii.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/libs/tiny-slider/tiny-slider.css')}}" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.png')}}" type="image/x-icon">
    <link rel="icon" href="{{asset('assets/images/favicon.png')}}" type="image/x-icon">
    <style>
        .logo a img{

                height: 120px;
                width: 140px;
        }
        .logo{
            display: contents;
        }
    </style>
   <!--[if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script><![endif]-->
<!--[if lt IE 9]><script src="js/respond.js"></script><![endif]-->
</head>

<body class="font-sans antialiased">
    <div class="page-wrapper">
	
        <!-- Cursor -->
        <div class="cursor"></div>
        <div class="cursor-follower"></div>
        <!-- Cursor End -->
         
        <!-- Preloader -->
        <div class="preloader"></div>
        {{-- mean header --}}
        @include('layouts.theme.mainheader')

        @yield('content')

        @include('layouts.theme.mainfooter')

        <!-- Search Popup -->
        <div class="search-popup">
            <div class="color-layer"></div>
            <button class="close-search"><span class="flaticon-close-1"></span></button>
            <form method="post" action="blog.html">
                <div class="form-group">
                    <input type="search" name="search-field" value="" placeholder="Search Here" required="">
                    <button class="fa fa-solid fa-magnifying-glass fa-fw" type="submit"></button>
                </div>
            </form>
        </div>
        <!-- End Search Popup -->
    </div>
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
        </svg>
    </div>

    @include('layouts.theme.scripts')
</body>

</html>
