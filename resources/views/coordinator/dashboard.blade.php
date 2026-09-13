<x-coordinator-layout>
    <p class="eyebrow">Overview</p>
    <h1 class="headline text-3xl text-ink sm:text-4xl">{{ $club?->name ?? 'Your club' }}</h1>

    @if ($club && ! $club->isApproved())
        <div class="panel-soft mt-4 border-butter bg-butter/10 p-4 text-sm">
            <p class="font-semibold text-ink">Your club is awaiting admin approval.</p>
            <p class="mt-1 text-ink-soft">You can still set up your club page and draft events now — everything goes live the moment it's approved.</p>
        </div>
    @endif

    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="panel p-5">
            <p class="font-mono text-[11px] uppercase text-ink-mute">Events</p>
            <p class="mt-1 font-display text-3xl text-ink">{{ $stats['total_events'] }}</p>
        </div>
        <div class="panel p-5">
            <p class="font-mono text-[11px] uppercase text-ink-mute">Upcoming</p>
            <p class="mt-1 font-display text-3xl text-tangerine">{{ $stats['upcoming_events'] }}</p>
        </div>
        <div class="panel p-5">
            <p class="font-mono text-[11px] uppercase text-ink-mute">Registrations</p>
            <p class="mt-1 font-display text-3xl text-ink">{{ $stats['total_registered'] }}</p>
        </div>
        <div class="panel p-5">
            <p class="font-mono text-[11px] uppercase text-ink-mute">Followers</p>
            <p class="mt-1 font-display text-3xl text-plum">{{ $stats['followers'] }}</p>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-between">
        <h2 class="font-display text-2xl text-ink">Your events</h2>
        <a href="{{ route('coordinator.events.create') }}" class="btn-accent btn-sm">+ New event</a>
    </div>

    @if ($events->isEmpty())
        <div class="panel-soft mt-4 py-14 text-center">
            <p class="text-ink-soft">No events yet — post your first one.</p>
            <a href="{{ route('coordinator.events.create') }}" class="btn-ink btn-sm mt-3 inline-flex">Create an event</a>
        </div>
    @else
        <div class="mt-4 overflow-x-auto rounded-2xl border-2 border-ink">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="border-b-2 border-ink bg-ink text-cream">
                    <tr>
                        <th class="px-4 py-3 font-mono text-[11px] uppercase tracking-wider">Event</th>
                        <th class="px-4 py-3 font-mono text-[11px] uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 font-mono text-[11px] uppercase tracking-wider">Sign-ups</th>
                        <th class="px-4 py-3 font-mono text-[11px] uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/10 bg-cream">
                    @foreach ($events as $event)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-ink">{{ Str::limit($event->name, 32) }}</td>
                            <td class="px-4 py-3 text-ink-soft">{{ $event->start_time->format('M d, Y') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('coordinator.events.registrations', $event) }}" class="text-tangerine hover:underline">{{ $event->active_registrations_count }}@if($event->max_participants)/{{ $event->max_participants }}@endif</a>
                            </td>
                            <td class="px-4 py-3">
                                @if ($event->isOngoing())<span class="chip !border-red-700 text-red-700">Live</span>
                                @elseif ($event->isPast())<span class="chip !border-ink/15">Past</span>
                                @else <span class="chip !border-moss text-moss">Upcoming</span>@endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('coordinator.events.edit', $event) }}" class="btn-quiet text-xs">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-coordinator-layout>
