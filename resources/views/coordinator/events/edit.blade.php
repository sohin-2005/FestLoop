<x-coordinator-layout>
    <p class="eyebrow">Edit event</p>
    <h1 class="headline text-3xl text-ink">{{ $event->name }}</h1>

    <form method="POST" action="{{ route('coordinator.events.update', $event) }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @method('PATCH')
        @include('coordinator.events._form')

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-accent">Save changes</button>
            <a href="{{ route('coordinator.events.index') }}" class="btn-ghost">Cancel</a>
            <form method="POST" action="{{ route('coordinator.events.destroy', $event) }}" onsubmit="return confirm('Delete this event? This cannot be undone.')" class="ml-auto">
                @csrf @method('DELETE')
                <button class="btn-danger">Delete event</button>
            </form>
        </div>
    </form>

    @if ($event->isPast())
        <div class="panel mt-8 p-6">
            <h2 class="font-display text-xl text-ink">Recap</h2>
            <p class="mt-1 text-sm text-ink-soft">Share how it went — this shows on your club's public page under "What we've done".</p>
            <form method="POST" action="{{ route('coordinator.events.recap', $event) }}" class="mt-3">
                @csrf
                <textarea name="recap" rows="3" class="field" placeholder="e.g. 80 students showed up, we raised ₹15,000 for...">{{ old('recap', $event->recap) }}</textarea>
                <button class="btn-ink btn-sm mt-3">Save recap</button>
            </form>
        </div>
    @endif
</x-coordinator-layout>
