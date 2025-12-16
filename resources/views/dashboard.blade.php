<x-app-layout>
    <x-slot name="header">
        @php
            $isCoordinator = Auth::guard('coordinator')->check();
            $user = $isCoordinator ? Auth::guard('coordinator')->user() : Auth::user();
        @endphp

        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white">
                    FestLoop Dashboard
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $isCoordinator ? "Manage your events" : "Discover college events" }}
                </p>
            </div>

            {{-- Coordinator Only: Create Event --}}
            @if ($isCoordinator)
                <a href="{{ route('events.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 
                   rounded-xl font-semibold text-sm text-white shadow-lg hover:shadow-xl hover:-translate-y-0.5 
                   transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create New Event
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Welcome Banner --}}
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8 md:p-10">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div class="text-white mb-6 md:mb-0">
                            <h3 class="text-3xl font-bold mb-2">
                                Welcome back, {{ $user->name }}! 👋
                            </h3>
                            <p class="text-blue-100 text-lg">
                                {{ $isCoordinator ? 'Manage your events and engage with students' : 'Explore exciting events happening on campus' }}
                            </p>
                        </div>

                        <div class="flex-shrink-0">
                            <div class="w-32 h-32 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <p class="text-sm text-gray-600">Total Events</p>
                    <h3 class="text-4xl font-bold text-gray-900 dark:text-white">{{ $totalEvents }}</h3>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <p class="text-sm text-gray-600">Upcoming Events</p>
                    <h3 class="text-4xl font-bold text-gray-900 dark:text-white">{{ $upcomingEventsCount }}</h3>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <p class="text-sm text-gray-600">Role</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $isCoordinator ? 'Coordinator' : 'Student' }}
                    </h3>
                </div>
            </div>

            {{-- Section Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Upcoming Events</h3>
                </div>

                <a href="{{ route('events.index') }}"
                   class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-800 dark:text-white">
                    View All Events →
                </a>
            </div>

            {{-- Events Grid --}}
            @if ($upcomingEvents->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-12 text-center">
                    <h3 class="text-xl font-semibold">No upcoming events</h3>
                    <p class="text-gray-600">Check again soon!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($upcomingEvents as $event)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                            <div class="h-48 bg-gray-200 dark:bg-gray-700">

                                {{-- Banner --}}
                                @if($event->banner_image)
                                    <img src="{{ asset('storage/' . $event->banner_image) }}"
                                         class="w-full h-full object-cover">
                                @endif
                            </div>

                            <div class="p-6">
                                <h4 class="text-xl font-bold">{{ $event->name }}</h4>
                                <p class="text-sm text-gray-600">📅 {{ $event->start_time->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-600">📍 {{ $event->location }}</p>

                                <div class="flex gap-2 mt-4">
                                    <a href="{{ route('events.show', $event) }}"
                                       class="flex-1 px-4 py-2 bg-blue-600 text-white rounded">
                                        View
                                    </a>

                                    @if (!$isCoordinator)
                                        <a href="{{ route('events.register', $event) }}"
                                           class="px-4 py-2 bg-green-600 text-white rounded">
                                            Register
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
