<x-app-layout>

    <x-slot name="header">
        <h2 class="text-2xl font-bold">Coordinator Profile</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto mt-6">

        <form method="POST" action="{{ route('coordinator.profile.update') }}">
            @csrf
            @method('PATCH')

            <div>
                <x-input-label value="Name" />
                <x-text-input type="text" name="name" class="w-full"
                    value="{{ $coordinator->name }}" required />
            </div>

            <div class="mt-4">
                <x-input-label value="Email" />
                <x-text-input type="email" name="email" class="w-full"
                    value="{{ $coordinator->email }}" required />
            </div>

            <div class="mt-4">
                <x-input-label value="New Password (optional)" />
                <x-text-input type="password" name="password" class="w-full" />
            </div>

            <div class="mt-4">
                <x-input-label value="Confirm Password" />
                <x-text-input type="password" name="password_confirmation" class="w-full" />
            </div>

            <x-primary-button class="mt-6">
                Save Changes
            </x-primary-button>

        </form>

    </div>
</x-app-layout>
