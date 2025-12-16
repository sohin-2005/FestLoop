<x-guest-layout>
    <div class="max-w-md mx-auto mt-10">

        <div class="mb-6 text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                Coordinator Login
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Sign in to manage events
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <x-input-error :messages="$errors->all()" class="mb-4" />

        <form method="POST" action="{{ url('/coordinator/login') }}">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input
                    id="email"
                    class="block mt-1 w-full"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                />
            </div>

            <div class="mt-4">
                <x-input-label for="password" value="Password" />
                <x-text-input
                    id="password"
                    class="block mt-1 w-full"
                    type="password"
                    name="password"
                    required
                />
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('login') }}"
                   class="text-sm text-blue-600 hover:underline">
                    Student Login
                </a>

                <x-primary-button>
                    Login
                </x-primary-button>
            </div>
        </form>

        <!-- Coordinator Register Link -->
        <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
            New coordinator?
            <a href="{{ url('/coordinator/register') }}"
               class="text-blue-600 hover:underline font-semibold">
                Register here
            </a>
        </p>

    </div>
</x-guest-layout>
