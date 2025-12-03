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
                    Create New Event
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Fill in the details to create an exciting event
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                {{-- Event Banner Image --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Banner</h3>
                    
                    <div class="mt-2">
                        <label class="block">
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors cursor-pointer">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-gray-600 dark:text-gray-400 mb-2">Click to upload event banner</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">PNG, JPG up to 5MB</p>
                            </div>
                            <input type="file" name="banner_image" accept="image/*" class="hidden">
                        </label>
                    </div>
                </div>

                {{-- Basic Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Basic Information</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Event Name *
                            </label>
                            <input type="text" name="name" id="name" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Annual Tech Fest 2025">
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Category *
                            </label>
                            <select name="category" id="category" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select a category</option>
                                <option value="cultural">Cultural</option>
                                <option value="technical">Technical</option>
                                <option value="sports">Sports</option>
                                <option value="academic">Academic</option>
                                <option value="social">Social</option>
                                <option value="workshop">Workshop</option>
                                <option value="competition">Competition</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description *
                            </label>
                            <textarea name="description" id="description" rows="5" required
                                      class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Describe your event in detail..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Date & Time --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Date & Time</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Start Date & Time *
                            </label>
                            <input type="datetime-local" name="start_time" id="start_time" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                End Date & Time
                            </label>
                            <input type="datetime-local" name="end_time" id="end_time"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                {{-- Location & Venue --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Location & Venue</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Venue *
                            </label>
                            <input type="text" name="location" id="location" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Main Auditorium, Building A">
                        </div>

                        <div>
                            <label for="venue_details" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Additional Venue Details
                            </label>
                            <textarea name="venue_details" id="venue_details" rows="3"
                                      class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Floor number, room number, directions, etc."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Registration Details --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Registration Details</h3>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="max_participants" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Maximum Participants
                                </label>
                                <input type="number" name="max_participants" id="max_participants" min="1"
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Leave empty for unlimited">
                            </div>

                            <div>
                                <label for="registration_deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Registration Deadline
                                </label>
                                <input type="datetime-local" name="registration_deadline" id="registration_deadline"
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="requires_approval" value="1"
                                       class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Require coordinator approval for registration
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Additional Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Additional Information</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="organizer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Organizer/Department
                            </label>
                            <input type="text" name="organizer" id="organizer"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Computer Science Department">
                        </div>

                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Contact Email
                            </label>
                            <input type="email" name="contact_email" id="contact_email"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="coordinator@college.edu">
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Contact Phone
                            </label>
                            <input type="tel" name="contact_phone" id="contact_phone"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="+91 1234567890">
                        </div>

                        <div>
                            <label for="rules" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rules & Guidelines
                            </label>
                            <textarea name="rules" id="rules" rows="4"
                                      class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="List any rules or guidelines participants should know..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('dashboard') }}"
                       class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 rounded-lg font-medium transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        Create Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>