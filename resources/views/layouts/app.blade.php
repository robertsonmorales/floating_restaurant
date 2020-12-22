<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Sunrise Floating Restaurant') }}</title>

    @include('includes.vendors')

    <link href="{{ asset('css/vendors.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet"> -->

    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo/favicon.png') }}">
    
    <script src="{{ asset('js/vendors.js') }}"></script>   
    <script src="{{ asset('js/app.js') }}" defer></script>

</head>
<body>
    <div class="container-fluid p-0" id="app">
        <div class="row no-gutters">
            <nav class="col col-md-4 col-lg-3 col-xl-2 vh-100 d-none d-lg-flex flex-column align-items-center position-fixed" id="sidebar">
                @include('includes.sidebar')
            </nav>
            <main class="col col-lg-9 col-xl-10 offset-lg-3 offset-xl-2 vh-100">
                @include('includes.header')
                @include('includes.breadcrumb')
                @yield('content')
            </main>
        </div>
    </div>
    @yield('scripts')
</body>
</html>
