<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title') - {{ config('app.name', 'Sunrise Floating Restaurant') }}</title>
    
    @include('includes.vendors')
    
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo/favicon.png') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script> 

</head>
<body>
    <div class="auth-container" style="background-image: url('{{ asset('images/svg/i-like-food.svg') }}');">
        <div class="auth-content">
            @yield('login')
        </div>
    </div>
    @yield('scripts')
</body>
</html>