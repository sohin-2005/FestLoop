<x-app-layout>
    {{-- =========================================================
         HERO
    ========================================================== --}}
    <section class="relative overflow-hidden border-b-2 border-ink bg-cream">
        <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-butter/50 blur-3xl"></div>
        <div class="pointer-events-none absolute -left-16 bottom-0 h-56 w-56 rounded-full bg-sky/30 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-20">
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-5">
                <div class="lg:col-span-3">
                    <span class="sticker bg-butter">✦ {{ \App\Models\Club::approved()->count() }} clubs, one loop</span>
                    <h1 class="headline mt-5 text-[13vw] text-ink sm:text-6xl lg:text-7xl">
                        Every club.<br>
                        <span class="relative inline-block">
                            Every event.
                            <svg class="absolute -bottom-2 left-0 w-full" height="10" viewBox="0 0 200 10" preserveAspectRatio="none"><path d="M0 6 Q 25 0, 50 6 T 100 6 T 150 6 T 200 6" stroke="#F2762E" stroke-width="4" fill="none"/></svg>
                        </span><br>
                        One loop.
                    </h1>
                    <p class="mt-6 max-w-lg text-lg text-ink-soft">
                        Stop checking {{ \App\Models\Club::approved()->count() }} different club pages. FestLoop is where every society on campus posts what's happening next, and what they've already pulled off.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="#browse" class="btn-ink">Browse events <span aria-hidden="true">↓</span></a>
                        <a href="{{ route('clubs.index') }}" class="btn-ghost">Explore clubs</a>
                        @guest
                            <a href="{{ route('coordinator.register') }}" class="btn-quiet ml-1">Running a club? Register it →</a>
                        @endguest
                    </div>
                </div>

                {{-- spotlight ticket --}}
                @if ($spotlight)
                    <div class="lg:col-span-2">
                        <a href="{{ route('events.show', $spotlight) }}" class="ticket block">
                            <div class="flex items-center justify-between border-b-2 border-ink bg-ink px-4 py-2 text-cream">
                                <span class="font-mono text-[10px] uppercase tracking-[0.16em]">Next up on campus</span>
                                <span class="font-mono text-[10px] uppercase tracking-[0.16em]">#{{ str_pad($spotlight->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="relative h-44 bg-gradient-to-br from-tangerine via-plum to-sky">
                                @if ($spotlight->banner_url)
                                    <img src="{{ $spotlight->banner_url }}" class="h-full w-full object-cover" alt="">
                                @endif
                                <span class="chip absolute left-3 top-3 bg-cream">{{ $spotlight->club?->short_name ?? Str::limit($spotlight->club?->name, 14) }}</span>
                            </div>
                            <div class="p-5">
                                <p class="eyebrow">{{ $spotlight->start_time->isoFormat('ddd, MMM D · h:mm A') }} · {{ $spotlight->location }}</p>
                                <h3 class="mt-1 font-display text-2xl leading-tight text-ink">{{ $spotlight->name }}</h3>
                                <p class="mt-2 text-sm text-ink-soft">{{ $spotlight->excerpt }}</p>
                            </div>
                            <div class="perforation mx-5"></div>
                            <div class="flex items-center justify-between px-5 py-4">
                                <span class="font-mono text-xs text-ink-soft">by {{ $spotlight->club?->name }}</span>
                                <span class="btn-quiet">Details →</span>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- =========================================================
         BROWSE / FILTER + GRID
    ========================================================== --}}
    <section id="browse" x-data="eventBrowser()" x-init="init()" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="eyebrow">Browse</p>
                <h2 class="headline text-3xl text-ink sm:text-4xl">What's on</h2>
            </div>
            <p class="font-mono text-xs text-ink-mute" x-show="!loading">
                <span x-text="total"></span> event<span x-show="total !== 1">s</span> found
            </p>
        </div>

        {{-- search --}}
        <div class="relative mt-6">
            <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            <input type="text" x-model="filters.search" @input.debounce.400ms="fetchEvents()"
                   placeholder="Search events, clubs, venues…"
                   class="field w-full !rounded-full !py-3.5 pl-11 pr-4 text-base shadow-hard-sm">
        </div>

        {{-- filter chips --}}
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <template x-for="opt in timeOptions" :key="opt.value">
                <button type="button" @click="filters.time = opt.value; fetchEvents()"
                        class="chip" :class="filters.time === opt.value ? 'chip-active' : 'hover:border-ink'">
                    <span x-text="opt.label"></span>
                </button>
            </template>

            <span class="mx-1 h-5 w-px bg-ink/15"></span>

            <select x-model="filters.category" @change="fetchEvents()" class="chip cursor-pointer border-ink/25 bg-cream pr-7">
                <option value="">All categories</option>
                @foreach (\App\Models\Event::CATEGORIES as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <select x-model="filters.club" @change="fetchEvents()" class="chip cursor-pointer border-ink/25 bg-cream pr-7">
                <option value="">All clubs</option>
                @foreach ($clubs as $club)
                    <option value="{{ $club->slug }}">{{ $club->name }}</option>
                @endforeach
            </select>

            <button type="button" x-show="hasActiveFilters()" @click="resetFilters()" class="btn-quiet ml-1 text-xs">Clear filters ✕</button>
        </div>

        {{-- grid --}}
        <div class="relative mt-8 min-h-[200px]">
            <div x-show="loading" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <template x-for="n in 6" :key="n">
                    <div class="h-80 animate-pulse rounded-2xl border-2 border-ink/10 bg-ink/5"></div>
                </template>
            </div>

            <div x-show="!loading && events.length" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <template x-for="event in events" :key="event.id">
                    <a :href="event.url" class="ticket group">
                        <div class="relative h-40 overflow-hidden bg-gradient-to-br from-tangerine via-plum to-sky">
                            <img x-show="event.banner_url" :src="event.banner_url" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="">
                            <div class="absolute left-3 top-3 flex flex-col items-center justify-center rounded-lg border-2 border-ink bg-cream px-2.5 py-1 leading-none shadow-hard-sm">
                                <span class="font-mono text-[9px] uppercase tracking-wider text-tangerine" x-text="event.start_month"></span>
                                <span class="font-display text-lg text-ink" x-text="event.start_day"></span>
                            </div>
                            <span x-show="event.is_ongoing" class="sticker absolute right-3 top-3 border-red-700 bg-red-600 text-cream"><span class="live-dot"></span> LIVE</span>
                            <span x-show="!event.is_ongoing && event.spots_left === 0" class="sticker absolute right-3 top-3 bg-ink text-cream">Full — join waitlist</span>
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <p class="eyebrow flex items-center gap-1.5">
                                <span class="flex h-4 w-4 items-center justify-center rounded-full bg-plum text-[8px] font-bold text-cream" x-text="event.club_initials"></span>
                                <span x-text="event.club_name"></span>
                            </p>
                            <h3 class="mt-1.5 font-display text-xl leading-tight text-ink" x-text="event.name"></h3>
                            <p class="mt-2 line-clamp-2 flex-1 text-sm text-ink-soft" x-text="event.excerpt"></p>
                        </div>
                        <div class="perforation mx-5"></div>
                        <div class="flex items-center justify-between px-5 py-3.5 font-mono text-[11px] text-ink-soft">
                            <span class="chip !border-ink/15" x-text="event.category_label"></span>
                            <span x-text="event.registrations_count + ' going'"></span>
                        </div>
                    </a>
                </template>
            </div>

            <div x-show="!loading && !events.length" class="panel-soft mx-auto max-w-md py-16 text-center">
                <p class="font-display text-2xl text-ink">No events match that</p>
                <p class="mt-2 text-sm text-ink-soft">Try a different search or clear your filters.</p>
                <button type="button" @click="resetFilters()" class="btn-ghost btn-sm mt-4">Clear filters</button>
            </div>
        </div>
    </section>

    @push('scripts') @endpush
    <script>
        function eventBrowser() {
            return {
                loading: true,
                total: 0,
                events: [],
                filters: { search: '', category: '', club: '', time: '' },
                timeOptions: [
                    { value: '', label: 'All upcoming' },
                    { value: 'week', label: 'This week' },
                    { value: 'ongoing', label: 'Happening now' },
                    { value: 'past', label: 'Past events' },
                ],
                init() {
                    this.fetchEvents();
                },
                hasActiveFilters() {
                    return this.filters.search || this.filters.category || this.filters.club || this.filters.time;
                },
                resetFilters() {
                    this.filters = { search: '', category: '', club: '', time: '' };
                    this.fetchEvents();
                },
                fetchEvents() {
                    this.loading = true;
                    const params = new URLSearchParams(Object.fromEntries(Object.entries(this.filters).filter(([, v]) => v)));
                    fetch(`{{ route('events.search') }}?${params.toString()}`)
                        .then((r) => r.json())
                        .then((data) => {
                            this.events = data.events;
                            this.total = data.total;
                            this.loading = false;
                        })
                        .catch(() => { this.loading = false; });
                },
            };
        }
    </script>
</x-app-layout>
