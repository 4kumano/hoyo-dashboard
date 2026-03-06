<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body>
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-[#0b0f19] text-slate-300 font-sans overflow-hidden">
        <livewire:dashboard.partials.sidebar />

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden relative">
            <livewire:dashboard.partials.navbar />

            {{ $slot }}

            <x-toast />

            @livewireScripts
        </main>
    </div>
</body>

</html>