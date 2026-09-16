@php
    $studentUser = auth()->user();
    $coordinator = auth('coordinator')->user();
@endphp

<header class="sticky top-0 z-40 border-b-2 border-ink bg-paper/90 backdrop-blur">
    {{-- ticker strip --}}
    <div class="overflow-hidden border-b border-ink/15 bg-ink py-1.5">
        <div class="flex w-max animate-marquee whitespace-nowrap font-mono text-[11px] uppercase tracking-[0.16em] text-cream/80">
            @for ($i = 0; $i < 2; $i++)
                <span class="mx-4 flex items-center gap-4">
                    <span>✦ One campus, every club, one loop</span>
                    <span>✦ Discover events happening this week</span>
                    <span>✦ Running a club? Post your next event free</span>
                    <span>✦ Follow clubs to get notified instantly</span>
                </span>
            @endfor
        </div>
    </div>

    <nav x-data="{ mobileOpen: false }" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-ink bg-tangerine font-display text-lg text-ink shadow-hard-sm">FL</span>
                    <span class="font-display text-xl tracking-wide text-ink">FestLoop</span>
                </a>

                <div class="hidden items-center gap-1 lg:flex">
                    <a href="{{ route('events.index') }}" class="rounded-full px-3.5 py-2 text-sm font-semibold {{ request()->routeIs('events.index') ? 'bg-ink text-cream' : 'text-ink hover:bg-ink/5' }}">Events</a>
                    <a href="{{ route('clubs.index') }}" class="rounded-full px-3.5 py-2 text-sm font-semibold {{ request()->routeIs('clubs.*') ? 'bg-ink text-cream' : 'text-ink hover:bg-ink/5' }}">Clubs</a>
                    @if ($studentUser)
                        <a href="{{ route('dashboard') }}" class="rounded-full px-3.5 py-2 text-sm font-semibold {{ request()->routeIs('dashboard') ? 'bg-ink text-cream' : 'text-ink hover:bg-ink/5' }}">My Dashboard</a>
                    @endif
                </div>
            </div>

            <div class="hidden items-center gap-3 lg:flex">
                @if ($studentUser)
                    <a href="{{ route('notifications.index') }}" class="relative flex h-9 w-9 items-center justify-center rounded-full border-2 border-ink text-ink hover:bg-ink hover:text-cream">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @php $unread = $studentUser->unreadNotifications()->count(); @endphp
                        @if ($unread)
                            <span class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full border border-ink bg-tangerine text-[9px] font-bold text-ink">{{ $unread > 9 ? '9+' : $unread }}</span>
                        @endif
                    </a>
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 rounded-full border-2 border-ink py-1.5 pl-1.5 pr-3 text-sm font-semibold hover:bg-ink hover:text-cream">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-plum text-[11px] font-bold text-cream">{{ strtoupper(substr($studentUser->name, 0, 1)) }}</span>
                            {{ Str::limit($studentUser->name, 14) }}
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-48 rounded-xl border-2 border-ink bg-cream py-1 shadow-hard-sm">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-ink/5">Profile settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="block w-full px-4 py-2 text-left text-sm hover:bg-ink/5">Log out</button>
                            </form>
                        </div>
                    </div>
                @elseif ($coordinator)
                    <a href="{{ route('coordinator.dashboard') }}" class="btn-ghost btn-sm">Club dashboard</a>
                @else
                    <a href="{{ route('coordinator.login') }}" class="btn-quiet">For clubs</a>
                    <a href="{{ route('login') }}" class="btn-ghost btn-sm">Log in</a>
                    <a href="{{ route('register') }}" class="btn-ink btn-sm">Sign up</a>
                @endif
            </div>

            <button @click="mobileOpen = !mobileOpen" class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-ink lg:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div x-show="mobileOpen" x-cloak x-transition class="space-y-1 border-t-2 border-ink py-3 lg:hidden">
            <a href="{{ route('events.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-ink/5">Events</a>
            <a href="{{ route('clubs.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-ink/5">Clubs</a>
            @if ($studentUser)
                <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-ink/5">My dashboard</a>
                <a href="{{ route('notifications.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-ink/5">Notifications</a>
                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-ink/5">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="block w-full rounded-lg px-3 py-2 text-left text-sm font-semibold hover:bg-ink/5">Log out</button>
                </form>
            @elseif ($coordinator)
                <a href="{{ route('coordinator.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-ink/5">Club dashboard</a>
            @else
                <a href="{{ route('coordinator.login') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-ink/5">For clubs</a>
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-ink/5">Log in</a>
                <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-ink/5">Sign up</a>
            @endif
        </div>
    </nav>
</header>
