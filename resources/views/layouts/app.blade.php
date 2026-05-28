<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Aplikasi Laporan Kerja') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body
    x-data="{ sidebarOpen: false }"
    x-bind:class="{ 'overflow-hidden': sidebarOpen }"
    class="font-sans antialiased bg-gray-100"
>
    <div class="min-h-screen">

        @auth
            @include('layouts.mobile-sidebar')
            @include('layouts.sidebar')
        @endauth

        <div class="lg:pl-64">
            @auth
                @include('layouts.topbar')
            @endauth

            <main class="p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>

    </div>

    @livewireScripts
</body>
</html>