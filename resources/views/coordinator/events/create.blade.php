<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 dark:text-white">
            Create New Event
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <form method="POST" action="{{ route('coordinator.events.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Event Name -->
                    <div>
                        <x-input-label for="name" :value="__('Event Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="4"
                                  class="block mt-1 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-brand focus:border-brand"
                                  required>{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Location -->
                    <div>
                        <x-input-label for="location" :value="__('Location')" />
                        <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" required />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>

                    <!-- Category -->
                    <div>
                        <x-input-label for="category" :value="__('Category')" />
                        <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" :value="old('category')" />
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>

                    <!-- Start Time -->
                    <div>
                        <x-input-label for="start_time" :value="__('Start Time')" />
                        <x-text-input id="start_time" class="block mt-1 w-full" type="datetime-local" name="start_time" :value="old('start_time')" required />
                        <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                    </div>

                    <!-- End Time -->
                    <div>
                        <x-input-label for="end_time" :value="__('End Time (Optional)')" />
                        <x-text-input id="end_time" class="block mt-1 w-full" type="datetime-local" name="end_time" :value="old('end_time')" />
                        <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                    </div>

                    <!-- Banner Image -->
                    <div>
                        <x-input-label for="banner_image" :value="__('Banner Image')" />
                        <input id="banner_image" class="block mt-1 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               type="file" name="banner_image" accept="image/*" />
                        <x-input-error :messages="$errors->get('banner_image')" class="mt-2" />
                    </div>

                    <!-- Max Participants -->
                    <div>
                        <x-input-label for="max_participants" :value="__('Max Participants (Optional)')" />
                        <x-text-input id="max_participants" class="block mt-1 w-full" type="number" name="max_participants" :value="old('max_participants')" />
                        <x-input-error :messages="$errors->get('max_participants')" class="mt-2" />
                    </div>

                    <!-- Registration Deadline -->
                    <div>
                        <x-input-label for="registration_deadline" :value="__('Registration Deadline (Optional)')" />
                        <x-text-input id="registration_deadline" class="block mt-1 w-full" type="datetime-local" name="registration_deadline" :value="old('registration_deadline')" />
                        <x-input-error :messages="$errors->get('registration_deadline')" class="mt-2" />
                    </div>

                    <!-- Contact Email -->
                    <div>
                        <x-input-label for="contact_email" :value="__('Contact Email (Optional)')" />
                        <x-text-input id="contact_email" class="block mt-1 w-full" type="email" name="contact_email" :value="old('contact_email')" />
                        <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                    </div>

                    <!-- Contact Phone -->
                    <div>
                        <x-input-label for="contact_phone" :value="__('Contact Phone (Optional)')" />
                        <x-text-input id="contact_phone" class="block mt-1 w-full" type="text" name="contact_phone" :value="old('contact_phone')" />
                        <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                    </div>

                    <!-- Rules -->
                    <div>
                        <x-input-label for="rules" :value="__('Rules (Optional)')" />
                        <textarea id="rules" name="rules" rows="3"
                                  class="block mt-1 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-brand focus:border-brand">{{ old('rules') }}</textarea>
                        <x-input-error :messages="$errors->get('rules')" class="mt-2" />
                    </div>

                    <!-- Requires Approval -->
                    <div class="flex items-center">
                        <input id="requires_approval" type="checkbox" name="requires_approval" class="rounded dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-brand shadow-sm focus:ring-brand"
                               {{ old('requires_approval') ? 'checked' : '' }} />
                        <label for="requires_approval" class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Require approval for registrations') }}
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('coordinator.events.index') }}"
                           class="px-6 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                            Cancel
                        </a>
                        <x-primary-button class="ms-3">
                            {{ __('Create Event') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
