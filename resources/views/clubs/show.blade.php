<x-app-layout>
    {{-- cover --}}
    <section class="relative h-56 border-b-2 border-ink sm:h-64" style="background: linear-gradient(135deg, {{ $club->accent_color }}, #171A3D)">
        @if ($club->cover_url)
            <img src="{{ $club->cover_url }}" class="h-full w-full object-cover" alt="">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-ink/60 to-transparent"></div>
    </section>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 pt-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex items-end gap-4">
                <div class="-mt-16 flex h-28 w-28 shrink-0 items-center justify-center rounded-2xl border-2 border-ink bg-cream font-display text-3xl shadow-hard" style="color: {{ $club->accent_color }}">
                    @if ($club->logo_url)
                        <img src="{{ $club->logo_url }}" class="h-full w-full rounded-2xl object-cover" alt="">
                    @else
                        {{ $club->initials }}
                    @endif
                </div>
                <div class="pb-1">
                    <span class="chip !border-ink/15 bg-cream">{{ $club->category_label }}</span>
                    <h1 class="headline mt-1 text-3xl text-ink sm:text-4xl">{{ $club->name }}</h1>
                    @if ($club->tagline)<p class="text-ink-soft">{{ $club->tagline }}</p>@endif
                </div>
            </div>

            <div class="flex items-center gap-3 pb-1">
                <span class="font-mono text-xs text-ink-mute">{{ $followerCount }} follower{{ $followerCount === 1 ? '' : 's' }}</span>
                @auth
                    <form method="POST" action="{{ $isFollowing ? route('clubs.unfollow', $club) : route('clubs.follow', $club) }}">
                        @csrf
                        @if ($isFollowing) @method('DELETE') @endif
                        <button class="{{ $isFollowing ? 'btn-ghost' : 'btn-accent' }} btn-sm">{{ $isFollowing ? 'Following ✓' : '+ Follow club' }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-accent btn-sm">+ Follow club</a>
                @endauth
            </div>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-10 pb-16 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-2">
                @if ($club->about)
                    <div class="panel p-6 sm:p-8">
                        <h2 class="font-display text-2xl text-ink">About {{ $club->name }}</h2>
                        <p class="mt-3 whitespace-pre-line leading-relaxed text-ink-soft">{{ $club->about }}</p>
                        @if ($club->mission)
                            <p class="mt-4 border-l-4 border-tangerine pl-4 font-serif text-lg italic text-ink">“{{ $club->mission }}”</p>
                        @endif
                    </div>
                @endif

                {{-- announcements --}}
                @if ($club->announcements->isNotEmpty())
                    <div>
                        <h2 class="font-display text-2xl text-ink">Announcements</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($club->announcements as $a)
                                <div class="panel-soft p-4">
                                    <div class="flex items-center gap-2">
                                        @if ($a->is_pinned)<span class="text-tangerine">📌</span>@endif
                                        <p class="font-semibold text-ink">{{ $a->title }}</p>
                                        <span class="ml-auto font-mono text-[10px] text-ink-mute">{{ $a->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-1.5 text-sm text-ink-soft">{{ $a->body }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- upcoming events --}}
                <div>
                    <h2 class="font-display text-2xl text-ink">Upcoming events</h2>
                    @if ($upcomingEvents->isEmpty())
                        <p class="mt-3 text-sm text-ink-soft">Nothing scheduled yet — check back soon.</p>
                    @else
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($upcomingEvents as $event)
                                <a href="{{ route('events.show', $event) }}" class="ticket">
                                    <div class="flex items-center justify-between p-4">
                                        <div>
                                            <p class="eyebrow">{{ $event->start_time->format('D, M j · g:i A') }}</p>
                                            <p class="mt-1 font-display text-lg text-ink">{{ $event->name }}</p>
                                            <p class="text-xs text-ink-soft">{{ $event->location }}</p>
                                        </div>
                                        <span class="chip !border-ink/15">{{ $event->category_label }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- past events / recap --}}
                @if ($pastEvents->isNotEmpty())
                    <div>
                        <h2 class="font-display text-2xl text-ink">What we've done</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($pastEvents as $event)
                                <a href="{{ route('events.show', $event) }}" class="panel-soft flex items-center gap-4 p-4 hover:border-ink">
                                    <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg border-2 border-ink/20 leading-none">
                                        <span class="font-mono text-[9px] uppercase text-ink-mute">{{ $event->start_time->format('M') }}</span>
                                        <span class="font-display text-base text-ink">{{ $event->start_time->format('d') }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-ink">{{ $event->name }}</p>
                                        <p class="truncate text-xs text-ink-soft">{{ $event->recap ?? $event->excerpt }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- sidebar --}}
            <div class="space-y-6">
                {{-- achievements --}}
                @if ($club->achievements->isNotEmpty())
                    <div class="panel p-6">
                        <h3 class="eyebrow">🏆 Achievements</h3>
                        <ul class="mt-3 space-y-4">
                            @foreach ($club->achievements as $achievement)
                                <li class="border-l-2 border-butter pl-3">
                                    <p class="font-semibold text-ink">{{ $achievement->title }}</p>
                                    @if ($achievement->achieved_on)<p class="font-mono text-[11px] text-ink-mute">{{ $achievement->achieved_on->format('M Y') }}</p>@endif
                                    @if ($achievement->description)<p class="mt-0.5 text-sm text-ink-soft">{{ $achievement->description }}</p>@endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- roadmap --}}
                @if ($club->roadmapItems->isNotEmpty())
                    <div class="panel p-6">
                        <h3 class="eyebrow">🧭 What's next</h3>
                        <ul class="mt-3 space-y-4">
                            @foreach ($club->roadmapItems as $item)
                                <li class="flex gap-3">
                                    <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ ['planned' => 'bg-ink/20', 'in_progress' => 'bg-tangerine', 'done' => 'bg-moss'][$item->status] }}"></span>
                                    <div>
                                        <p class="text-sm font-semibold text-ink">{{ $item->title }}</p>
                                        <p class="font-mono text-[11px] text-ink-mute">{{ $item->status_label }}@if($item->target_label) · {{ $item->target_label }}@endif</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- contact --}}
                <div class="panel p-6 text-sm">
                    <h3 class="eyebrow">Connect</h3>
                    <ul class="mt-3 space-y-2">
                        @if ($club->email)<li><a href="mailto:{{ $club->email }}" class="font-semibold text-ink hover:text-tangerine">{{ $club->email }}</a></li>@endif
                        @if ($club->website)<li><a href="{{ $club->website }}" target="_blank" class="text-ink-soft hover:text-tangerine">Website ↗</a></li>@endif
                        @if ($club->instagram)<li><a href="https://instagram.com/{{ ltrim($club->instagram, '@') }}" target="_blank" class="text-ink-soft hover:text-tangerine">Instagram ↗</a></li>@endif
                        @if ($club->linkedin)<li><a href="{{ $club->linkedin }}" target="_blank" class="text-ink-soft hover:text-tangerine">LinkedIn ↗</a></li>@endif
                        @if ($club->faculty_advisor)<li class="text-ink-soft">Faculty advisor: {{ $club->faculty_advisor }}</li>@endif
                        @if ($club->founded_year)<li class="text-ink-soft">Founded {{ $club->founded_year }}</li>@endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
