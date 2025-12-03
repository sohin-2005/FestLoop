<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}" class="mr-4 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white">
                    Event Details
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        @if ($event->banner_image)
    <img src="{{ asset('storage/' . $event->banner_image) }}"
         alt="Event banner"
         class="w-full max-h-80 object-cover rounded-xl mb-6">
@endif

            {{-- Event Banner --}}
            <div class="mb-8 rounded-2xl overflow-hidden shadow-2xl">
                <div class="h-96 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 relative">
                    @if($event->banner_image)
                        <img src="{{ asset('storage/' . $event->banner_image) }}" 
                             alt="{{ $event->name }}"
                             class="w-full h-full object-cover">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-8">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-3">
                                    {{ ucfirst($event->category ?? 'Event') }}
                                </span>
                                <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">
                                    {{ $event->name }}
                                </h1>
                                <p class="text-white/90 text-lg">
                                    Organized by {{ $event->organizer ?? 'College' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Description --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">About This Event</h2>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                            {{ $event->description }}
                        </p>
                    </div>

                    {{-- Venue Details --}}
                    @if($event->venue_details)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Venue Information</h2>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $event->venue_details }}
                        </p>
                    </div>
                    @endif

                    {{-- Rules & Guidelines --}}
                    @if($event->rules)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Rules & Guidelines</h2>
                        <div class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                            {{ $event->rules }}
                        </div>
                    </div>
                    @endif

                    {{-- Registered Participants (For coordinators) --}}
                    @if(auth()->user()->role === 'coordinator')
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Registered Participants</h2>
                            <span class="px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg font-semibold">
                                {{ $event->registrations_count ?? 0 }} Registered
                            </span>
                        </div>
                        {{-- Add participants list here --}}
                    </div>
                    @endif

                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Quick Info Card --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Event Information</h3>
                        
                        <div class="space-y-5">
                            {{-- Date & Time --}}
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Date & Time</p>
                                    <p class="text-gray-900 dark:text-white font-medium">
                                        {{ $event->start_time?->format('d M Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ $event->start_time?->format('h:i A') }}
                                        @if($event->end_time)
                                            - {{ $event->end_time->format('h:i A') }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Location --}}
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Location</p>
                                    <p class="text-gray-900 dark:text-white font-medium">
                                        {{ $event->location }}
                                    </p>
                                </div>
                            </div>

                            {{-- Capacity --}}
                            @if($event->max_participants)
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Capacity</p>
                                    <p class="text-gray-900 dark:text-white font-medium">
                                        {{ $event->registrations_count ?? 0 }} / {{ $event->max_participants }} registered
                                    </p>
                                    <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full" 
                                             style="width: {{ min(100, (($event->registrations_count ?? 0) / $event->max_participants) * 100) }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Registration Deadline --}}
                            @if($event->registration_deadline)
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Registration Deadline</p>
                                    <p class="text-gray-900 dark:text-white font-medium">
                                        {{ $event->registration_deadline->format('d M Y, h:i A') }}
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Registration Button --}}
                        @if(auth()->user()->role === 'student')
                            @php
                                $isRegistered = false; // Check if user is already registered
                                $isFull = $event->max_participants && ($event->registrations_count ?? 0) >= $event->max_participants;
                                $isDeadlinePassed = $event->registration_deadline && now() > $event->registration_deadline;
                            @endphp

                            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                @if($isRegistered)
                                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                        <svg class="w-12 h-12 mx-auto text-green-600 dark:text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-green-800 dark:text-green-300 font-semibold">You're Registered!</p>
                                    </div>
                                @elseif($isFull)
                                    <button disabled
                                            class="w-full px-6 py-4 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-xl font-semibold cursor-not-allowed">
                                        Event Full
                                    </button>
                                @elseif($isDeadlinePassed)
                                    <button disabled
                                            class="w-full px-6 py-4 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-xl font-semibold cursor-not-allowed">
                                        Registration Closed
                                    </button>
                                @else
                                    <form action="{{ route('events.register', $event) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                            Register Now
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif

                        {{-- Contact Information --}}
                        @if($event->contact_email || $event->contact_phone)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Contact</h4>
                            <div class="space-y-2">
                                @if($event->contact_email)
                                <a href="mailto:{{ $event->contact_email }}" 
                                   class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $event->contact_email }}
                                </a>
                                @endif
                                @if($event->contact_phone)
                                <a href="tel:{{ $event->contact_phone }}" 
                                   class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $event->contact_phone }}
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Edit/Delete for Coordinators --}}
                        @if(auth()->user()->role === 'coordinator')
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
                            <a href="{{ route('events.edit', $event) }}"
                               class="block w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-lg font-medium transition-colors">
                                Edit Event
                            </a>
                            <form action="{{ route('events.destroy', $event) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to delete this event?')"
                                        class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    Delete Event
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>