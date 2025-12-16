<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-900 dark:text-white">
                My Events
            </h2>
            <a href="{{ route('coordinator.events.create') }}"
               class="px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand/90 transition">
                + Create Event
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($events->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        You haven't created any events yet.
                    </p>
                    <a href="{{ route('coordinator.events.create') }}"
                       class="inline-block px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand/90 transition">
                        Create Your First Event
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6">
                    @foreach($events as $event)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                            {{ $event->name }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $event->start_time?->format('d M Y, h:i A') }}
                                        </p>
                                    </div>
                                    <span class="inline-block px-3 py-1 bg-brand/20 text-brand rounded-full text-sm font-medium">
                                        {{ $event->registrations_count ?? 0 }} registered
                                    </span>
                                </div>

                                <p class="text-gray-700 dark:text-gray-300 mb-4 line-clamp-2">
                                    {{ $event->description }}
                                </p>

                                <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Location:</span>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $event->location }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Category:</span>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $event->category ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <a href="{{ route('events.show', $event) }}"
                                       class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-center text-sm font-medium">
                                        View
                                    </a>
                                    <a href="{{ route('coordinator.events.edit', $event) }}"
                                       class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center text-sm font-medium">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('coordinator.events.destroy', $event) }}"
                                          onsubmit="return confirm('Are you sure?')" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
