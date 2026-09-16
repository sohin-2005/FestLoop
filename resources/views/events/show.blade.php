<x-app-layout>
    {{-- banner --}}
    <section class="relative h-72 overflow-hidden border-b-2 border-ink bg-gradient-to-br from-tangerine via-plum to-sky sm:h-96">
        @if ($event->banner_url)
            <img src="{{ $event->banner_url }}" class="h-full w-full object-cover" alt="{{ $event->name }}">
        @endif
        {{-- Club posters are often light/cream, so the scrim has to stay strong
             enough for the white title to read over them. --}}
        <div class="absolute inset-0 bg-gradient-to-t from-ink/95 via-ink/65 to-ink/25"></div>

        <div class="absolute inset-x-0 bottom-0 mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
            <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1 rounded-full border-2 border-cream bg-ink/40 px-3 py-1 text-xs font-semibold text-cream backdrop-blur hover:bg-ink/60">← All events</a>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span class="chip !border-cream bg-cream">{{ $event->category_label }}</span>
                @if ($event->isOngoing())
                    <span class="sticker border-red-700 bg-red-600 text-cream"><span class="live-dot"></span> Live now</span>
                @elseif ($event->isPast())
                    <span class="sticker bg-ink text-cream">Wrapped up</span>
                @endif
            </div>
            <h1 class="headline mt-3 text-4xl text-cream sm:text-5xl">{{ $event->name }}</h1>
            <a href="{{ route('clubs.show', $event->club) }}" class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-cream/90 hover:text-butter">
                <span class="flex h-6 w-6 items-center justify-center rounded-full border border-cream bg-plum text-[10px] font-bold">{{ $event->club->initials }}</span>
                Hosted by {{ $event->club->name }}
            </a>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            {{-- main --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="panel p-6 sm:p-8">
                    <h2 class="font-display text-2xl text-ink">About this event</h2>
                    <p class="mt-3 whitespace-pre-line leading-relaxed text-ink-soft">{{ $event->description }}</p>
                </div>

                @if ($event->venue_details)
                    <div class="panel p-6 sm:p-8">
                        <h2 class="font-display text-2xl text-ink">Venue details</h2>
                        <p class="mt-3 whitespace-pre-line leading-relaxed text-ink-soft">{{ $event->venue_details }}</p>
                    </div>
                @endif

                @if ($event->rules)
                    <div class="panel p-6 sm:p-8">
                        <h2 class="font-display text-2xl text-ink">Rules & guidelines</h2>
                        <p class="mt-3 whitespace-pre-line leading-relaxed text-ink-soft">{{ $event->rules }}</p>
                    </div>
                @endif

                @if ($event->recap)
                    <div class="panel p-6 sm:p-8" style="border-color:#3C8D5A">
                        <p class="eyebrow text-moss">✦ How it went</p>
                        <p class="mt-2 whitespace-pre-line leading-relaxed text-ink-soft">{{ $event->recap }}</p>
                    </div>
                @endif

                @if ($relatedEvents->isNotEmpty())
                    <div>
                        <h2 class="font-display text-2xl text-ink">More from {{ $event->club->name }}</h2>
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                            @foreach ($relatedEvents as $related)
                                <a href="{{ route('events.show', $related) }}" class="panel-soft block p-4 hover:border-ink">
                                    <p class="eyebrow">{{ $related->start_time->format('M d') }}</p>
                                    <p class="mt-1 font-semibold text-ink">{{ Str::limit($related->name, 40) }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- sidebar: ticket / register --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-5">
                    <div class="panel p-6 shadow-hard">
                        <dl class="space-y-4 text-sm">
                            <div class="flex gap-3">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-tangerine" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <div>
                                    <dt class="field-label !mb-0.5">When</dt>
                                    <dd class="font-semibold text-ink">{{ $event->start_time->isoFormat('dddd, MMM D, YYYY') }}</dd>
                                    <dd class="text-ink-soft">{{ $event->start_time->format('h:i A') }}@if($event->end_time) – {{ $event->end_time->format('h:i A') }} @endif</dd>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-sky" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <div>
                                    <dt class="field-label !mb-0.5">Where</dt>
                                    <dd class="font-semibold text-ink">{{ $event->location }}</dd>
                                    <dd class="text-ink-soft">{{ $event->mode_label }}</dd>
                                </div>
                            </div>
                            @if ($event->max_participants)
                                <div class="flex gap-3">
                                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-plum" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 10-3-6.66"/></svg>
                                    <div>
                                        <dt class="field-label !mb-0.5">Capacity</dt>
                                        <dd class="font-semibold text-ink">{{ $event->seatsTaken() }} / {{ $event->max_participants }} spots filled</dd>
                                        <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full border border-ink/20 bg-paper">
                                            <div class="h-full bg-tangerine" style="width: {{ min(100, round($event->seatsTaken() / max(1,$event->max_participants) * 100)) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ($event->registration_deadline)
                                <div class="flex gap-3">
                                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <div>
                                        <dt class="field-label !mb-0.5">Registration closes</dt>
                                        <dd class="font-semibold text-ink">{{ $event->registration_deadline->format('M d, Y \a\t h:i A') }}</dd>
                                    </div>
                                </div>
                            @endif
                        </dl>

                        <div class="perforation my-5"></div>

                        {{-- registration action --}}
                        @auth
                            @if ($registration)
                                <div class="rounded-xl border-2 border-ink bg-butter/30 p-4 text-center">
                                    <p class="font-display text-lg text-ink">{{ $registration->label }}</p>
                                    @if (in_array($registration->status, ['registered', 'pending']))
                                        <form method="POST" action="{{ route('events.register.cancel', $event) }}" class="mt-3" onsubmit="return confirm('Cancel your registration?')">
                                            @csrf @method('DELETE')
                                            <button class="btn-quiet text-xs">Cancel registration</button>
                                        </form>
                                    @endif
                                </div>
                            @elseif ($event->usesExternalRegistration())
                                <a href="{{ $event->external_registration_url }}" target="_blank" rel="noopener" class="btn-accent w-full">Register on club site ↗</a>
                            @elseif (! $event->registrationOpen())
                                <button class="btn-ink w-full" disabled>Registration closed</button>
                            @elseif ($event->isFull())
                                <form method="POST" action="{{ route('events.register', $event) }}">
                                    @csrf
                                    <button class="btn-ink w-full">Join waitlist</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('events.register', $event) }}">
                                    @csrf
                                    <button class="btn-accent w-full">{{ $event->requires_approval ? 'Request to join' : 'Register now' }}</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-ink w-full">Log in to register</a>
                        @endauth
                    </div>

                    @if ($event->contact_email || $event->contact_phone)
                        <div class="panel-soft p-5 text-sm">
                            <p class="field-label">Questions?</p>
                            @if ($event->contact_email)
                                <a href="mailto:{{ $event->contact_email }}" class="mt-1 block font-semibold text-ink hover:text-tangerine">{{ $event->contact_email }}</a>
                            @endif
                            @if ($event->contact_phone)
                                <p class="mt-1 text-ink-soft">{{ $event->contact_phone }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
