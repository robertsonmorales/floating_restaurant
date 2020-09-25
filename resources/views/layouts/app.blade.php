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
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo/favicon.png') }}">
    
    <script src="{{ asset('js/vendors.js') }}"></script>   
    <script src="{{ asset('js/app.js') }}" defer></script>    

</head>
<body>
    <div class="admin-container" id="app">
        @include('includes.sidebar')
        <main>
            @include('includes.header')
            @include('includes.breadcrumb')
            @yield('content')
        </main>
    </div>
    <script type="text/javascript">
        @if(session()->get('success'))
        session('{{ session()->get('success') }}');
        function session(msg){
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-right',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: false,
            });
                
            Toast.fire({
                icon: 'success',
                title: msg
            });
        }
        @endif
    </script>
    @yield('scripts')
</body>
</html>
