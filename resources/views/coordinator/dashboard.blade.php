<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 dark:text-white">
            Coordinator Dashboard
        </h2>
        <form method="POST" action="{{ route('coordinator.logout') }}" class="inline">
    @csrf
    <button class="px-3 py-1 text-sm bg-red-600 text-white rounded-md hover:bg-red-700">
        Logout
    </button>
</form>

    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:pwhtax-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <a href="{{ route('coordinator.events.create') }}"
       class="block bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition">
        <h3 class="text-xl font-bold mb-2">Create Event</h3>
        <p class="text-blue-100 text-sm">
            Add a new college event
        </p>
    </a>
</div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                    Your Events
                </h3>
             
                @if($events->isEmpty())
                    <p class="text-gray-600 dark:text-gray-400">
                        You haven’t created any events yet.
                    </p>
                @else
                    <ul class="space-y-2">
                        @foreach($events as $event)
                            <li class="p-4 border rounded-lg dark:border-gray-700">
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ $event->name }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $event->start_time?->format('d M Y, h:i A') }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
