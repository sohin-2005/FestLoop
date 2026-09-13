<x-guest-layout>
    <p class="eyebrow text-center">Club coordinator</p>
    <h1 class="headline mt-1 text-center text-2xl text-ink">Log in to your club</h1>

    @if (session('status'))
        <div class="mt-4 rounded-xl border-2 border-moss bg-moss/10 p-3 text-sm text-moss">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-xl border-2 border-red-700 bg-red-700/10 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('coordinator.login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="field-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="field">
        </div>
        <div>
            <label class="field-label">Password</label>
            <input type="password" name="password" required class="field">
        </div>
        <label class="flex items-center gap-2 text-sm text-ink-soft">
            <input type="checkbox" name="remember" class="rounded border-ink/30 text-tangerine focus:ring-tangerine">
            Remember me
        </label>
        <button type="submit" class="btn-ink w-full">Log in</button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-soft">
        New club? <a href="{{ route('coordinator.register') }}" class="font-semibold text-tangerine hover:underline">Register it here</a>
    </p>
    <p class="mt-2 text-center text-sm text-ink-soft">
        Student instead? <a href="{{ route('login') }}" class="font-semibold text-ink hover:underline">Student login</a>
    </p>
</x-guest-layout>
