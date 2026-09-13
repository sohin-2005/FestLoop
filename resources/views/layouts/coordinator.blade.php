<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ isset($title) ? $title.' · FestLoop Club Panel' : 'Club Panel · FestLoop' }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Anton&family=Instrument+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-paper">
        @php $club = auth('coordinator')->user()?->club; @endphp

        <div x-data="{ navOpen: false }" class="lg:flex">
            {{-- sidebar --}}
            <aside :class="navOpen ? 'block' : 'hidden'" class="w-full shrink-0 border-b-2 border-ink bg-ink text-cream lg:block lg:w-64 lg:min-h-screen lg:border-b-0 lg:border-r-2">
                <div class="p-5">
                    <a href="{{ route('coordinator.dashboard') }}" class="flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-cream bg-tangerine font-display text-lg text-ink">FL</span>
                        <span class="font-display text-lg">FestLoop</span>
                    </a>
                    @if ($club)
                        <p class="mt-4 truncate font-mono text-[11px] uppercase tracking-wider text-cream/50">Managing</p>
                        <p class="truncate font-semibold text-cream">{{ $club->name }}</p>
                        @if (! $club->isApproved())
                            <span class="sticker mt-2 inline-flex border-butter bg-butter/20 text-butter">{{ ucfirst($club->status) }} review</span>
                        @endif
                    @endif
                </div>
                <nav class="space-y-1 px-3 pb-6">
                    <a href="{{ route('coordinator.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('coordinator.dashboard') ? 'bg-cream text-ink' : 'text-cream/80 hover:bg-cream/10' }}">Dashboard</a>
                    <a href="{{ route('coordinator.events.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('coordinator.events.*') ? 'bg-cream text-ink' : 'text-cream/80 hover:bg-cream/10' }}">Events</a>
                    <a href="{{ route('coordinator.club.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('coordinator.club.*') ? 'bg-cream text-ink' : 'text-cream/80 hover:bg-cream/10' }}">Club page</a>
                    @if ($club?->isApproved())
                        <a href="{{ route('clubs.show', $club) }}" target="_blank" class="block rounded-lg px-3 py-2 text-sm font-semibold text-cream/80 hover:bg-cream/10">View public page ↗</a>
                    @endif
                    <form method="POST" action="{{ route('coordinator.logout') }}" class="pt-2">
                        @csrf
                        <button class="block w-full rounded-lg px-3 py-2 text-left text-sm font-semibold text-cream/60 hover:bg-cream/10">Log out</button>
                    </form>
                </nav>
            </aside>

            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between border-b-2 border-ink bg-cream px-4 py-3 lg:hidden">
                    <span class="font-display text-lg">Club Panel</span>
                    <button @click="navOpen = !navOpen" class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-ink">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>

                @if (session('success'))
                    <div class="border-b-2 border-ink bg-moss px-4 py-2.5 text-sm font-semibold text-cream sm:px-6">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="border-b-2 border-ink bg-red-700 px-4 py-2.5 text-sm font-semibold text-cream sm:px-6">{{ session('error') }}</div>
                @endif

                <main class="px-4 py-8 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
