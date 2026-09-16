<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ isset($title) ? $title.' · FestLoop' : 'FestLoop' }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Anton&family=Instrument+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-ink">
        <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-10">
            {{-- background confetti dots --}}
            <div class="pointer-events-none absolute inset-0 opacity-30" style="background-image: radial-gradient(#F2762E 1.5px, transparent 1.5px), radial-gradient(#2E8FD6 1.5px, transparent 1.5px); background-size: 42px 42px; background-position: 0 0, 21px 21px;"></div>

            <a href="{{ route('home') }}" class="relative z-10 mb-6 flex items-center gap-2">
                <span class="flex h-11 w-11 items-center justify-center rounded-full border-2 border-cream bg-tangerine font-display text-xl text-ink shadow-hard-sm">FL</span>
                <span class="font-display text-2xl tracking-wide text-cream">FestLoop</span>
            </a>

            <div class="relative z-10 w-full max-w-md">
                <div class="panel px-6 py-8 shadow-hard-lg sm:px-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
