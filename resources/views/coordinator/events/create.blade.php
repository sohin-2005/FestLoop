<x-coordinator-layout>
    <p class="eyebrow">New event</p>
    <h1 class="headline text-3xl text-ink">Post an event</h1>

    <form method="POST" action="{{ route('coordinator.events.store') }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @include('coordinator.events._form')

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-accent">Publish event</button>
            <a href="{{ route('coordinator.events.index') }}" class="btn-ghost">Cancel</a>
        </div>
    </form>
</x-coordinator-layout>
