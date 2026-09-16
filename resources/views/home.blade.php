<x-app-layout>
    {{-- =====================================================================
         HERO — dark, kinetic, built from real club marks instead of stock video
    ====================================================================== --}}
    <section class="relative overflow-hidden bg-ink text-cream">
        {{-- confetti dot field --}}
        <div class="pointer-events-none absolute inset-0 opacity-[0.18]"
             style="background-image: radial-gradient(#F2762E 1.5px, transparent 1.5px), radial-gradient(#2E8FD6 1.5px, transparent 1.5px); background-size: 46px 46px; background-position: 0 0, 23px 23px;"></div>
        <div class="pointer-events-none absolute -left-32 top-10 h-72 w-72 rounded-full bg-plum/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-tangerine/30 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-20 lg:pt-20">
            @if ($liveNow->isNotEmpty())
                <a href="{{ route('events.show', $liveNow->first()) }}"
                   class="mb-7 inline-flex items-center gap-2 rounded-full border-2 border-cream/30 bg-cream/10 px-4 py-1.5 text-xs font-semibold backdrop-blur transition hover:border-cream/60">
                    <span class="live-dot"></span>
                    <span class="font-mono uppercase tracking-[0.14em]">Happening now</span>
                    <span class="text-cream/70">{{ Str::limit($liveNow->first()->name, 34) }}</span>
                </a>
            @else
                <span class="sticker mb-7 inline-flex border-butter bg-butter text-ink">✦ {{ $stats['clubs'] }} clubs · one loop</span>
            @endif

            <h1 class="headline text-[15vw] leading-[0.86] sm:text-7xl lg:text-[5.5rem]">
                <span class="block reveal">Every event</span>
                <span class="block reveal" style="--d:.08s">on campus,</span>
                <span class="block reveal text-tangerine" style="--d:.16s">
                    in one loop.
                </span>
            </h1>

            <p class="reveal mt-7 max-w-xl text-lg text-cream/70" style="--d:.24s">
                Clubs post what they're running. You find it, register once, and get a
                nudge before it starts. No more hunting through {{ $stats['clubs'] }} separate pages.
            </p>

            <div class="reveal mt-9 flex flex-wrap items-center gap-3" style="--d:.3s">
                <a href="{{ route('events.index') }}" class="btn-accent">Browse events</a>
                <a href="{{ route('clubs.index') }}" class="btn inline-flex border-cream/40 bg-transparent text-cream hover:bg-cream hover:text-ink">Explore clubs</a>
                @guest
                    <a href="{{ route('register') }}" class="btn-quiet ml-1 text-cream decoration-cream/30 hover:decoration-tangerine">Create your student account →</a>
                @endguest
            </div>

            {{-- live counters --}}
            <dl class="reveal mt-14 grid max-w-2xl grid-cols-2 gap-6 sm:grid-cols-4" style="--d:.36s">
                @php
                    // Only show a number we actually have. A "0 sign-ups" tile on the
                    // front page is worse than one tile fewer.
                    $tiles = collect([
                        ['n' => $stats['clubs'],         'l' => 'Clubs'],
                        ['n' => $stats['events'],        'l' => 'Events listed'],
                        ['n' => $stats['upcoming'],      'l' => 'Coming up'],
                        ['n' => $stats['registrations'], 'l' => 'Sign-ups'],
                    ])->reject(fn ($t) => (int) $t['n'] === 0);
                @endphp
                @foreach ($tiles as $stat)
                    <div>
                        <dd class="font-display text-4xl text-cream counter" data-to="{{ $stat['n'] }}">0</dd>
                        <dt class="mt-1 font-mono text-[10px] uppercase tracking-[0.16em] text-cream/50">{{ $stat['l'] }}</dt>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- === the moving poster wall: two rows, opposite directions === --}}
        @if ($wallClubs->count() > 3)
            @php $half = $wallClubs->chunk((int) ceil($wallClubs->count() / 2)); @endphp
            <div class="relative space-y-3 pb-14">
                @foreach ([['animate-marquee', $half[0]], ['animate-marquee-reverse', $half[1] ?? $half[0]]] as [$anim, $row])
                    <div class="flex overflow-hidden">
                        <div class="flex w-max {{ $anim }} gap-3 pr-3">
                            @foreach ($row->concat($row) as $club)
                                <a href="{{ route('clubs.show', $club->slug) }}"
                                   class="group flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border-2 border-cream/15 bg-cream p-3 transition hover:-translate-y-1 hover:border-cream sm:h-24 sm:w-24"
                                   title="{{ $club->name }}">
                                    <img src="{{ asset('storage/'.$club->logo_path) }}" alt="{{ $club->name }}"
                                         class="h-full w-full object-contain opacity-80 transition group-hover:opacity-100" loading="lazy">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- =====================================================================
         SCROLLING WORD BAND
    ====================================================================== --}}
    <div class="overflow-hidden border-y-2 border-ink bg-butter py-2.5">
        <div class="flex w-max animate-marquee whitespace-nowrap font-display text-xl uppercase tracking-wide text-ink">
            @for ($i = 0; $i < 2; $i++)
                <span class="mx-3 flex items-center gap-3">
                    <span>Hackathons</span><span class="text-tangerine">✦</span>
                    <span>Quizzes</span><span class="text-tangerine">✦</span>
                    <span>Workshops</span><span class="text-tangerine">✦</span>
                    <span>Photowalks</span><span class="text-tangerine">✦</span>
                    <span>Fests</span><span class="text-tangerine">✦</span>
                    <span>Talks</span><span class="text-tangerine">✦</span>
                    <span>Meetups</span><span class="text-tangerine">✦</span>
                </span>
            @endfor
        </div>
    </div>

    {{-- =====================================================================
         SPOTLIGHT + WHAT'S COMING UP
    ====================================================================== --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Next up</p>
                <h2 class="headline text-4xl text-ink sm:text-5xl">Coming up on campus</h2>
            </div>
            <a href="{{ route('events.index') }}" class="btn-quiet">See all events →</a>
        </div>

        @if ($upcoming->isEmpty())
            <div class="panel-soft mt-8 py-16 text-center">
                <p class="font-display text-2xl text-ink">Nothing scheduled yet</p>
                <p class="mt-2 text-sm text-ink-soft">When a club posts an event, it shows up right here.</p>
            </div>
        @else
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($upcoming as $event)
                    <a href="{{ route('events.show', $event) }}" class="ticket reveal" style="--d:{{ $loop->index * .07 }}s">
                        <div class="relative h-36 overflow-hidden bg-gradient-to-br from-tangerine via-plum to-sky">
                            @if ($event->banner_url)
                                <img src="{{ $event->banner_url }}" class="h-full w-full object-cover" alt="" loading="lazy">
                            @endif
                            <div class="absolute left-3 top-3 flex flex-col items-center justify-center rounded-lg border-2 border-ink bg-cream px-2.5 py-1 leading-none shadow-hard-sm">
                                <span class="font-mono text-[9px] uppercase tracking-wider text-tangerine">{{ $event->start_time->format('M') }}</span>
                                <span class="font-display text-lg text-ink">{{ $event->start_time->format('d') }}</span>
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col p-4">
                            <p class="eyebrow">{{ $event->club?->name }}</p>
                            <h3 class="mt-1 font-display text-lg leading-tight text-ink">{{ $event->name }}</h3>
                            <p class="mt-1.5 line-clamp-2 flex-1 text-sm text-ink-soft">{{ $event->excerpt }}</p>
                        </div>
                        <div class="perforation mx-4"></div>
                        <div class="flex items-center justify-between px-4 py-3 font-mono text-[11px] text-ink-soft">
                            <span class="chip !border-ink/15">{{ $event->category_label }}</span>
                            <span>{{ $event->active_registrations_count }} going</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- =====================================================================
         HOW IT WORKS
    ====================================================================== --}}
    <section class="border-y-2 border-ink bg-cream">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <p class="eyebrow">How it works</p>
            <h2 class="headline text-4xl text-ink sm:text-5xl">Three steps, then show up.</h2>

            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ([
                    ['01', 'Find it', 'Every club posts here, so one search covers the whole campus. Filter by category, club, or what\'s on this week.'],
                    ['02', 'Register once', 'One tap with your account. If the club runs its own form, we send you straight there instead.'],
                    ['03', 'Get reminded', 'A nudge lands the day before. Full event? You join the waitlist and we tell you the moment a spot frees up.'],
                ] as $step)
                    <div class="panel reveal p-6" style="--d:{{ $loop->index * .09 }}s">
                        <span class="font-display text-5xl text-tangerine">{{ $step[0] }}</span>
                        <h3 class="mt-3 font-display text-2xl text-ink">{{ $step[1] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-soft">{{ $step[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================================
         FOR CLUBS
    ====================================================================== --}}
    <section class="relative overflow-hidden bg-ink text-cream">
        <div class="pointer-events-none absolute inset-0 opacity-[0.15]"
             style="background-image: radial-gradient(#FFD447 1.5px, transparent 1.5px); background-size: 40px 40px;"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
                <div class="max-w-xl">
                    <p class="eyebrow text-cream/50">For club heads</p>
                    <h2 class="headline mt-1 text-4xl sm:text-5xl">Run your club's page like a pro.</h2>
                    <p class="mt-4 text-cream/70">
                        Post events, handle sign-ups and approvals, export the attendance sheet as CSV,
                        and keep your achievements, roadmap and notices in one public page students actually visit.
                    </p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('coordinator.register') }}" class="btn-accent">Register your club</a>
                        <a href="{{ route('coordinator.login') }}" class="btn inline-flex border-cream/40 bg-transparent text-cream hover:bg-cream hover:text-ink">Coordinator login</a>
                    </div>
                </div>

                <ul class="grid w-full max-w-sm gap-3">
                    @foreach (['Your own public club page', 'Sign-ups, approvals & waitlists', 'CSV export for attendance', 'Followers get notified instantly'] as $perk)
                        <li class="flex items-center gap-3 rounded-xl border border-cream/15 bg-cream/5 px-4 py-3 text-sm">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-tangerine font-bold text-ink">✓</span>
                            {{ $perk }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <script>
        // Reveal on scroll — no library, respects reduced-motion.
        (function () {
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const items = document.querySelectorAll('.reveal');

            if (reduce || !('IntersectionObserver' in window)) {
                items.forEach(el => el.classList.add('is-in'));
            } else {
                const io = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-in');
                            io.unobserve(entry.target);
                        }
                    });
                }, { rootMargin: '0px 0px -8% 0px' });
                items.forEach(el => io.observe(el));
            }

            // Count up the hero stats once they're on screen.
            const counters = document.querySelectorAll('.counter');
            const run = (el) => {
                const to = parseInt(el.dataset.to, 10) || 0;
                if (reduce || to === 0) { el.textContent = to; return; }
                const start = performance.now();
                const dur = 900;
                const tick = (now) => {
                    const p = Math.min((now - start) / dur, 1);
                    el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3)));
                    if (p < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            };

            if ('IntersectionObserver' in window) {
                const co = new IntersectionObserver((entries) => {
                    entries.forEach(e => { if (e.isIntersecting) { run(e.target); co.unobserve(e.target); } });
                });
                counters.forEach(el => co.observe(el));
            } else {
                counters.forEach(run);
            }
        })();
    </script>
</x-app-layout>
