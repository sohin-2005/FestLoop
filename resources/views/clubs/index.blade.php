<x-app-layout>
    <section class="border-b-2 border-ink bg-cream">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <p class="eyebrow">Directory</p>
            <h1 class="headline text-4xl text-ink sm:text-5xl">Every club, one page each.</h1>
            <p class="mt-3 max-w-xl text-ink-soft">Follow the ones you care about to get notified the moment they post something new.</p>

            <form method="GET" class="mt-6 flex flex-wrap gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search clubs…" class="field max-w-xs !rounded-full">
                <button type="submit" class="btn-ghost btn-sm">Search</button>
                <a href="{{ route('clubs.index') }}" class="chip {{ !request('category') ? 'chip-active' : '' }}">All</a>
                @foreach ($categories as $value => $label)
                    <a href="{{ route('clubs.index', ['category' => $value]) }}" class="chip {{ request('category') === $value ? 'chip-active' : '' }}">{{ $label }}</a>
                @endforeach
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if ($clubs->isEmpty())
            <div class="panel-soft mx-auto max-w-md py-16 text-center">
                <p class="font-display text-2xl text-ink">No clubs found</p>
                <p class="mt-2 text-sm text-ink-soft">Try a different search or category.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($clubs as $club)
                    <a href="{{ route('clubs.show', $club) }}" class="panel group flex flex-col overflow-hidden hover:-translate-y-1 hover:shadow-hard-sm transition">
                        <div class="h-28 w-full" style="background: linear-gradient(135deg, {{ $club->accent_color }}, #171A3D)">
                            @if ($club->cover_url)
                                <img src="{{ $club->cover_url }}" class="h-full w-full object-cover" alt="">
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <div class="-mt-12 flex h-14 w-14 items-center justify-center rounded-full border-2 border-ink bg-cream font-display text-lg shadow-hard-sm" style="color: {{ $club->accent_color }}">
                                @if ($club->logo_url)
                                    <img src="{{ $club->logo_url }}" class="h-full w-full rounded-full object-cover" alt="">
                                @else
                                    {{ $club->initials }}
                                @endif
                            </div>
                            <h3 class="mt-3 font-display text-xl text-ink">{{ $club->name }}</h3>
                            <p class="mt-1 flex-1 text-sm text-ink-soft">{{ $club->tagline ?? Str::limit($club->about, 80) }}</p>
                            <div class="mt-4 flex items-center justify-between font-mono text-[11px] text-ink-mute">
                                <span class="chip !border-ink/15">{{ $club->category_label }}</span>
                                <span>{{ $club->followers_count }} followers · {{ $club->events_count }} events</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</x-app-layout>
