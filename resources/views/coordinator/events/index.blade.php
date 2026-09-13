<x-coordinator-layout>
    <div class="flex items-center justify-between">
        <div>
            <p class="eyebrow">Manage</p>
            <h1 class="headline text-3xl text-ink">Your events</h1>
        </div>
        <a href="{{ route('coordinator.events.create') }}" class="btn-accent btn-sm">+ New event</a>
    </div>

    @if ($events->isEmpty())
        <div class="panel-soft mt-6 py-14 text-center">
            <p class="text-ink-soft">No events yet.</p>
            <a href="{{ route('coordinator.events.create') }}" class="btn-ink btn-sm mt-3 inline-flex">Create your first event</a>
        </div>
    @else
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($events as $event)
                <div class="panel flex flex-col overflow-hidden">
                    <div class="relative h-28 bg-gradient-to-br from-tangerine via-plum to-sky">
                        @if ($event->banner_url)<img src="{{ $event->banner_url }}" class="h-full w-full object-cover" alt="">@endif
                        @if ($event->isOngoing())<span class="sticker absolute right-2 top-2 border-red-700 bg-red-600 text-cream">Live</span>@endif
                    </div>
                    <div class="flex-1 p-4">
                        <p class="eyebrow">{{ $event->start_time->format('M d, Y · g:i A') }}</p>
                        <p class="mt-1 font-display text-lg leading-tight text-ink">{{ $event->name }}</p>
                        <p class="mt-1 text-xs text-ink-soft">{{ $event->location }}</p>
                    </div>
                    <div class="flex items-center justify-between border-t border-ink/10 px-4 py-3 text-xs">
                        <a href="{{ route('coordinator.events.registrations', $event) }}" class="font-semibold text-tangerine hover:underline">{{ $event->active_registrations_count }} registered</a>
                        <div class="flex gap-3">
                            <a href="{{ route('coordinator.events.edit', $event) }}" class="btn-quiet">Edit</a>
                            <form method="POST" action="{{ route('coordinator.events.destroy', $event) }}" onsubmit="return confirm('Delete this event?')">
                                @csrf @method('DELETE')
                                <button class="btn-quiet text-red-700 decoration-red-700/30">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-coordinator-layout>
