<x-guest-layout>
    <p class="eyebrow text-center">Register your club</p>
    <h1 class="headline mt-1 text-center text-2xl text-ink">Bring your club to FestLoop</h1>
    <p class="mt-2 text-center text-sm text-ink-soft">Your club goes live after a quick admin review — usually same day.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-xl border-2 border-red-700 bg-red-700/10 p-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-0.5">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('coordinator.register') }}" class="mt-6 space-y-4">
        @csrf
        <div class="border-b border-dashed border-ink/20 pb-4">
            <p class="field-label">Club details</p>
            <div class="mt-2 space-y-3">
                <input type="text" name="club_name" value="{{ old('club_name') }}" placeholder="Club name" required class="field">
                <select name="category" required class="field">
                    <option value="">Category…</option>
                    @foreach ($categories as $value => $label)
                        <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="text" name="tagline" value="{{ old('tagline') }}" placeholder="One-line tagline (optional)" class="field">
            </div>
        </div>

        <div>
            <p class="field-label">Your account (first coordinator)</p>
            <div class="mt-2 space-y-3">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" required class="field">
                <input type="text" name="position" value="{{ old('position') }}" placeholder="Your role (e.g. President)" class="field">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required class="field">
                <input type="password" name="password" placeholder="Password" required class="field">
                <input type="password" name="password_confirmation" placeholder="Confirm password" required class="field">
            </div>
        </div>

        <button type="submit" class="btn-ink w-full">Register club</button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-soft">
        Already registered? <a href="{{ route('coordinator.login') }}" class="font-semibold text-tangerine hover:underline">Log in</a>
    </p>
</x-guest-layout>
