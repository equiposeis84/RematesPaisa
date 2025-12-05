<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Remates El Paísa')</title>
    <link rel="stylesheet" href="{{ asset('css/MainContent.css') }}">
    <link rel="stylesheet" href="{{ asset('css/SidebarStyle.css') }}">
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>