<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' · FestLoop' : 'FestLoop — Discover. Celebrate. Repeat.' }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Anton&family=Instrument+Sans:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen">
        @include('layouts.navigation')

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)" x-transition
                 class="fixed inset-x-0 top-20 z-50 mx-auto w-fit">
                <div class="flex items-center gap-2 rounded-full border-2 border-ink bg-moss px-5 py-2.5 text-sm font-semibold text-cream shadow-hard-sm">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)" x-transition
                 class="fixed inset-x-0 top-20 z-50 mx-auto w-fit">
                <div class="flex items-center gap-2 rounded-full border-2 border-ink bg-red-700 px-5 py-2.5 text-sm font-semibold text-cream shadow-hard-sm">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @isset($header)
            <header class="border-b-2 border-ink bg-cream">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            {{ $slot }}
        </main>

        <footer class="mt-20 border-t-2 border-ink bg-ink text-cream">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                    <div class="col-span-2">
                        <div class="flex items-center gap-2">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-cream bg-tangerine font-display text-lg text-ink">FL</span>
                            <span class="font-display text-xl">FestLoop</span>
                        </div>
                        <p class="mt-3 max-w-xs text-sm text-cream/70">One place for every club on campus to post events, share what they're up to, and grow their crowd.</p>
                    </div>
                    <div>
                        <p class="eyebrow text-cream/50">Explore</p>
                        <ul class="mt-3 space-y-2 text-sm text-cream/80">
                            <li><a href="{{ route('events.index') }}" class="hover:text-tangerine">All events</a></li>
                            <li><a href="{{ route('clubs.index') }}" class="hover:text-tangerine">All clubs</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="eyebrow text-cream/50">For clubs</p>
                        <ul class="mt-3 space-y-2 text-sm text-cream/80">
                            <li><a href="{{ route('coordinator.register') }}" class="hover:text-tangerine">Register your club</a></li>
                            <li><a href="{{ route('coordinator.login') }}" class="hover:text-tangerine">Coordinator login</a></li>
                        </ul>
                    </div>
                </div>
                <p class="mt-10 font-mono text-[11px] uppercase tracking-widest text-cream/40">© {{ date('Y') }} FestLoop · Built for campus clubs, by a club member.</p>
            </div>
        </footer>
    </body>
</html>
