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

    <link rel="shortcut icon" href="{{asset('assets/images/favicon.png')}}" type="image/x-icon">
    <link rel="icon" href="{{asset('assets/images/favicon.png')}}" type="image/x-icon">
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
