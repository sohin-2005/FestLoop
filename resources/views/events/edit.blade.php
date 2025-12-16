<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold">Edit Event</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto mt-6">
        <form method="POST" action="{{ route('events.update', $event) }}">
            @csrf
            @method('PATCH')

            <div>
                <x-input-label value="Event Name" />
                <x-text-input type="text" name="name" class="w-full"
                    value="{{ old('name', $event->name) }}" required />
            </div>

            <div class="mt-4">
                <x-input-label value="Start Time" />
                <x-text-input type="datetime-local" name="start_time" class="w-full"
                    value="{{ old('start_time', $event->start_time->format('Y-m-d\TH:i')) }}" required />
            </div>

            <div class="mt-4">
                <x-input-label value="Description" />
                <textarea name="description"
                    class="w-full rounded">{{ old('description', $event->description) }}</textarea>
            </div>

            <x-primary-button class="mt-6">Update Event</x-primary-button>
        </form>
    </div>
</x-app-layout>
