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
    class="overflow-x-hidden font-sans antialiased bg-slate-100 text-slate-900"
>
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.12),_transparent_32rem),linear-gradient(180deg,_#f8fafc_0%,_#eef2f7_100%)]">

        @auth
            @include('layouts.mobile-sidebar')
            @include('layouts.sidebar')
        @endauth

        <div class="min-w-0 lg:pl-64">
            @auth
                @include('layouts.topbar')
            @endauth

            <main class="min-w-0 overflow-x-hidden p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>

        <x-toast />
        <x-ui.confirm-modal />
    </div>

    @livewireScripts
</body>
</html>
