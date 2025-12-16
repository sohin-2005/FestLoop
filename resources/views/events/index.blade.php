<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white">
                    All Events
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Browse and discover all college events
                </p>
            </div>
            @auth
                @if (auth()->user()->role === 'coordinator')
                    <a href="{{ route('events.create') }}"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-xl font-semibold text-sm text-white shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Event
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filters & Search --}}
            <div class="mb-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <div class="space-y-4" id="filter-form">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Search --}}
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Search Events
                            </label>
                            <div class="relative">
                                <input type="text"
                                       name="search"
                                       id="search"
                                       value="{{ request('search') }}"
                                       placeholder="Search by name, description..."
                                       class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Category Filter --}}
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Category
                            </label>
                            <select name="category"
                                    id="category"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Categories</option>
                                <option value="cultural" {{ request('category') == 'cultural' ? 'selected' : '' }}>Cultural</option>
                                <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Technical</option>
                                <option value="sports" {{ request('category') == 'sports' ? 'selected' : '' }}>Sports</option>
                                <option value="academic" {{ request('category') == 'academic' ? 'selected' : '' }}>Academic</option>
                                <option value="social" {{ request('category') == 'social' ? 'selected' : '' }}>Social</option>
                                <option value="workshop" {{ request('category') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                <option value="competition" {{ request('category') == 'competition' ? 'selected' : '' }}>Competition</option>
                                <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        {{-- Time Filter --}}
                        <div>
                            <label for="time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Time Period
                            </label>
                            <select name="time"
                                    id="time"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Events</option>
                                <option value="upcoming" {{ request('time') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="ongoing" {{ request('time') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="past" {{ request('time') == 'past' ? 'selected' : '' }}>Past</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('events.index') }}"
                           class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 rounded-lg font-medium transition-colors">
                            Clear
                        </a>
                    </div>
                </div>
            </div>

            {{-- Loading Spinner --}}
            <div id="loading" class="hidden text-center py-8">
                <div class="inline-block">
                    <svg class="animate-spin h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            {{-- Events Grid --}}
            <div id="events-container">
                @if ($events->isEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-16">
                        <div class="text-center">
                            <svg class="w-32 h-32 mx-auto text-gray-300 dark:text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No events found</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">Try adjusting your filters or search terms</p>

                            @auth
                                @if (auth()->user()->role === 'coordinator')
                                    <a href="{{ route('events.create') }}"
                                       class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                                        Create Your First Event
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                @else
                    {{-- Event Stats Banner --}}
                    <div class="mb-6 flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-4 border border-blue-100 dark:border-gray-600">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300 font-medium" id="event-count">
                                Showing {{ $events->count() }} of {{ $events->total() }} events
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="events-grid">
                        @foreach ($events as $event)
                            @php
                                $now = now();
                                $isUpcoming = $event->start_time > $now;
                                $isPast = $event->end_time && $event->end_time < $now;
                                $isOngoing = !$isUpcoming && !$isPast;

                                $statusColor = $isOngoing ? 'blue' : ($isUpcoming ? 'green' : 'gray');
                                $statusText = $isOngoing ? 'Ongoing' : ($isUpcoming ? 'Upcoming' : 'Past');
                            @endphp

                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden group relative">

                                {{-- Status Badge --}}
                                <div class="absolute top-4 left-4 z-10">
                                    <span class="px-3 py-1 bg-{{ $statusColor }}-100 dark:bg-{{ $statusColor }}-900/30 text-{{ $statusColor }}-700 dark:text-{{ $statusColor }}-300 text-xs font-bold rounded-full shadow-lg">
                                        {{ $statusText }}
                                    </span>
                                </div>

                                {{-- Event Banner --}}
                                <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 relative overflow-hidden">
                                    @if ($event->banner_image)
                                        <img
                                            src="{{ asset('storage/' . $event->banner_image) }}"
                                            alt="{{ $event->name }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-white text-lg font-semibold">
                                            {{ \Illuminate\Support\Str::limit($event->name, 20) }}
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors duration-300"></div>

                                    <div class="absolute bottom-4 right-4">
                                        <span class="px-3 py-1 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm text-gray-900 dark:text-white text-xs font-semibold rounded-lg">
                                            {{ ucfirst($event->category ?? 'Event') }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Event Content --}}
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $event->name }}
                                    </h3>

                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                        {{ $event->description }}
                                    </p>

                                    <div class="space-y-2 mb-5">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>{{ $event->start_time?->format('d M Y, h:i A') ?? 'TBA' }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span class="line-clamp-1">{{ $event->location }}</span>
                                        </div>
                                    </div>

                                    {{-- Registration Info --}}
                                    @if ($event->max_participants)
                                        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="flex items-center justify-between text-sm mb-1">
                                                <span class="text-gray-600 dark:text-gray-400">Registrations</span>
                                                <span class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $event->registrations_count ?? 0 }} / {{ $event->max_participants }}
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5">
                                                @php
                                                    $percentage = min(100, (($event->registrations_count ?? 0) / $event->max_participants) * 100);
                                                @endphp
                                                <div class="bg-blue-600 h-1.5 rounded-full"
                                                     style="width: {{ $percentage }}%">
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Actions --}}
                                    <div class="flex gap-2">
                                        <a href="{{ route('events.show', $event) }}"
                                           class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors duration-200">
                                            View Details
                                        </a>

                                        @auth
                                            @if (auth()->user()->role === 'student' && !$isPast)
                                                <form method="POST" action="{{ route('events.register', $event) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors duration-200">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const categorySelect = document.getElementById('category');
        const timeSelect = document.getElementById('time');
        const eventsContainer = document.getElementById('events-container');
        const loadingSpinner = document.getElementById('loading');
        let debounceTimer;

        // Debounce search for 500ms
        function performSearch() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchEvents();
            }, 500);
        }

        // Fetch events with AJAX
        async function fetchEvents() {
            const search = searchInput.value;
            const category = categorySelect.value;
            const time = timeSelect.value;

            // Show loading spinner
            loadingSpinner.classList.remove('hidden');

            try {
                const response = await fetch(`/events/search?search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&time=${encodeURIComponent(time)}`);
                const data = await response.json();

                if (data.success) {
                    renderEvents(data.events, data.total);
                }
            } catch (error) {
                console.error('Error fetching events:', error);
            } finally {
                // Hide loading spinner
                loadingSpinner.classList.add('hidden');
            }
        }

        // Render events in the grid
        function renderEvents(events, total) {
            if (events.length === 0) {
                eventsContainer.innerHTML = `
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-16">
                        <div class="text-center">
                            <svg class="w-32 h-32 mx-auto text-gray-300 dark:text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No events found</h3>
                            <p class="text-gray-600 dark:text-gray-400">Try adjusting your filters or search terms</p>
                        </div>
                    </div>
                `;
                return;
            }

            // Render events grid
            const eventsHtml = events.map(event => `
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden group relative">
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 relative overflow-hidden">
                        ${event.banner_image ? `
                            <img src="{{ asset('') }}${event.banner_image}" alt="${event.name}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        ` : `
                            <div class="w-full h-full flex items-center justify-center text-white text-lg font-semibold">${event.name.substring(0, 20)}</div>
                        `}
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors duration-300"></div>
                        <div class="absolute bottom-4 right-4">
                            <span class="px-3 py-1 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm text-gray-900 dark:text-white text-xs font-semibold rounded-lg">
                                ${event.category || 'Event'}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            ${event.name}
                        </h3>

                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                            ${event.description}
                        </p>

                        <div class="space-y-2 mb-5">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>${event.start_time_short}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="line-clamp-1">${event.location || 'TBA'}</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="/events/${event.id}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors duration-200">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            `).join('');

            eventsContainer.innerHTML = `
                <div class="mb-6 flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-4 border border-blue-100 dark:border-gray-600">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">
                            Showing ${events.length} of ${total} events
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    ${eventsHtml}
                </div>
            `;
        }

        // Event listeners for real-time search
        searchInput.addEventListener('input', performSearch);
        categorySelect.addEventListener('change', fetchEvents);
        timeSelect.addEventListener('change', fetchEvents);

        // Initial load
        window.addEventListener('load', () => {
            // Only fetch if filters are applied
            if (new URLSearchParams(window.location.search).toString()) {
                fetchEvents();
            }
        });
    </script>
</x-app-layout>
