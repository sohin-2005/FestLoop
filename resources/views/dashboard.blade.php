<x-app-layout>
    <section class="border-b-2 border-ink bg-cream">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <p class="eyebrow">Welcome back</p>
            <h1 class="headline text-3xl text-ink sm:text-4xl">Hey, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            <div class="space-y-10 lg:col-span-2">
                {{-- registrations --}}
                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-2xl text-ink">Your upcoming events</h2>
                        <a href="{{ route('events.index') }}" class="btn-quiet text-sm">Browse more →</a>
                    </div>

                    @if ($upcomingRegistrations->isEmpty())
                        <div class="panel-soft mt-4 py-12 text-center">
                            <p class="text-ink-soft">You haven't registered for anything yet.</p>
                            <a href="{{ route('events.index') }}" class="btn-accent btn-sm mt-3 inline-flex">Find an event</a>
                        </div>
                    @else
                        <div class="mt-4 space-y-3">
                            @foreach ($upcomingRegistrations as $event)
                                <a href="{{ route('events.show', $event) }}" class="ticket flex-row items-center">
                                    <div class="flex flex-1 items-center gap-4 p-4">
                                        <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-lg border-2 border-ink leading-none">
                                            <span class="font-mono text-[9px] uppercase text-tangerine">{{ $event->start_time->format('M') }}</span>
                                            <span class="font-display text-xl text-ink">{{ $event->start_time->format('d') }}</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate font-display text-lg text-ink">{{ $event->name }}</p>
                                            <p class="truncate text-xs text-ink-soft">{{ $event->club?->name }} · {{ $event->location }}</p>
                                        </div>
                                        <span class="chip !border-ink/15 shrink-0">{{ \App\Models\Registration::LABELS[$event->pivot->status] ?? ucfirst($event->pivot->status) }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- for you --}}
                <div>
                    <h2 class="font-display text-2xl text-ink">
                        {{ $followedClubs->isNotEmpty() ? 'From clubs you follow' : 'Happening soon' }}
                    </h2>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @forelse ($forYouEvents as $event)
                            <a href="{{ route('events.show', $event) }}" class="panel-soft p-4 hover:border-ink">
                                <p class="eyebrow">{{ $event->start_time->format('D, M j') }}</p>
                                <p class="mt-1 font-semibold text-ink">{{ $event->name }}</p>
                                <p class="text-xs text-ink-soft">{{ $event->club?->name }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-ink-soft">No upcoming events right now — check back soon.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- followed clubs sidebar --}}
            <div>
                <div class="panel p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="eyebrow">Clubs you follow</h3>
                        <a href="{{ route('clubs.index') }}" class="text-xs font-semibold text-tangerine hover:underline">+ Discover</a>
                    </div>
                    @if ($followedClubs->isEmpty())
                        <p class="mt-3 text-sm text-ink-soft">Follow a club to get notified when they post events.</p>
                    @else
                        <ul class="mt-3 space-y-3">
                            @foreach ($followedClubs as $club)
                                <li>
                                    <a href="{{ route('clubs.show', $club) }}" class="flex items-center gap-3 hover:text-tangerine">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-ink bg-cream font-display text-xs" style="color: {{ $club->accent_color }}">{{ $club->initials }}</span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-sm font-semibold text-ink">{{ $club->name }}</span>
                                            <span class="block font-mono text-[10px] text-ink-mute">{{ $club->events_count }} events</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
