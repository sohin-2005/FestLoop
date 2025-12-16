<x-guest-layout>
    <div class="max-w-md mx-auto mt-10">

        <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white">
            Coordinator Registration
        </h2>

        <form method="POST" action="{{ url('/coordinator/register') }}" class="mt-6">
            @csrf

            <div>
                <x-input-label for="name" value="Name" />
                <x-text-input id="name" class="block mt-1 w-full"
                    type="text" name="name" value="{{ old('name') }}" required autofocus />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div class="mt-4">
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="block mt-1 w-full"
                    type="email" name="email" value="{{ old('email') }}" required />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" class="block mt-1 w-full"
                    type="password" name="password" required />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" value="Confirm Password" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                    type="password" name="password_confirmation" required />
            </div>

            <x-primary-button class="mt-6 w-full justify-center">
                Register
            </x-primary-button>

            <p class="mt-4 text-center text-sm">
                Already have an account?
                <a class="text-blue-600 hover:underline" href="{{ url('/coordinator/login') }}">
                    Login
                </a>
            </p>
        </form>

    </div>
</x-guest-layout>
