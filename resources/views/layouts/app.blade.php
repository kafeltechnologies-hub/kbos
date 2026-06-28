<!DOCTYPE html>
<html>
<head>
    <title>KBOS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-100">
    {{ $slot ?? '' }}

    @yield('content')

    @livewireScripts
</body>
</html>