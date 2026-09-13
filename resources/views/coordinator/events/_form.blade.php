@php
    $event = $event ?? null;
    $old = fn ($key, $default = '') => old($key, $event?->{$key} ?? $default);
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-5 lg:col-span-2">
        <div class="panel p-6">
            <label class="field-label">Event name</label>
            <input type="text" name="name" value="{{ $old('name') }}" required class="field text-lg font-semibold">
            @error('name')<p class="field-error">{{ $message }}</p>@enderror

            <label class="field-label mt-5">Description</label>
            <textarea name="description" rows="6" required class="field">{{ $old('description') }}</textarea>
            @error('description')<p class="field-error">{{ $message }}</p>@enderror

            <div class="mt-5 grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">Category</label>
                    <select name="category" required class="field">
                        @foreach (\App\Models\Event::CATEGORIES as $value => $label)
                            <option value="{{ $value }}" @selected($old('category') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Mode</label>
                    <select name="mode" required class="field">
                        @foreach (\App\Models\Event::MODES as $value => $label)
                            <option value="{{ $value }}" @selected($old('mode', 'offline') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="panel p-6">
            <label class="field-label">Location</label>
            <input type="text" name="location" value="{{ $old('location') }}" required placeholder="e.g. Main Auditorium" class="field">
            @error('location')<p class="field-error">{{ $message }}</p>@enderror

            <label class="field-label mt-5">Venue details (optional)</label>
            <textarea name="venue_details" rows="2" class="field">{{ $old('venue_details') }}</textarea>

            <div class="mt-5 grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">Starts</label>
                    <input type="datetime-local" name="start_time" value="{{ $old('start_time') ? \Illuminate\Support\Carbon::parse($old('start_time'))->format('Y-m-d\TH:i') : '' }}" required class="field">
                    @error('start_time')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="field-label">Ends (optional)</label>
                    <input type="datetime-local" name="end_time" value="{{ $old('end_time') ? \Illuminate\Support\Carbon::parse($old('end_time'))->format('Y-m-d\TH:i') : '' }}" class="field">
                    @error('end_time')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="panel p-6">
            <label class="field-label">Rules & guidelines (optional)</label>
            <textarea name="rules" rows="3" class="field">{{ $old('rules') }}</textarea>
        </div>
    </div>

    <div class="space-y-5">
        <div class="panel p-6">
            <label class="field-label">Banner image</label>
            @if ($event?->banner_url)
                <img src="{{ $event->banner_url }}" class="mb-2 h-28 w-full rounded-lg object-cover">
            @endif
            <input type="file" name="banner_image" accept="image/*" class="field !py-2 text-xs">
            @error('banner_image')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="panel p-6">
            <p class="field-label">Registration</p>

            <label class="field-label mt-3 !mb-1">Max participants (optional)</label>
            <input type="number" name="max_participants" min="1" value="{{ $old('max_participants') }}" placeholder="Unlimited" class="field">

            <label class="field-label mt-3 !mb-1">Registration deadline</label>
            <input type="datetime-local" name="registration_deadline" value="{{ $old('registration_deadline') ? \Illuminate\Support\Carbon::parse($old('registration_deadline'))->format('Y-m-d\TH:i') : '' }}" class="field">

            <label class="mt-3 flex items-center gap-2 text-sm">
                <input type="checkbox" name="requires_approval" value="1" @checked($old('requires_approval')) class="rounded border-ink/30 text-tangerine focus:ring-tangerine">
                Manually approve sign-ups
            </label>

            <label class="field-label mt-4 !mb-1">External registration link (optional)</label>
            <input type="url" name="external_registration_url" value="{{ $old('external_registration_url') }}" placeholder="https://forms.gle/…" class="field">
            <p class="mt-1 text-[11px] text-ink-mute">If set, students are sent here instead of registering on FestLoop.</p>
        </div>

        <div class="panel p-6">
            <p class="field-label">Contact</p>
            <label class="field-label mt-3 !mb-1">Email</label>
            <input type="email" name="contact_email" value="{{ $old('contact_email') }}" class="field">
            <label class="field-label mt-3 !mb-1">Phone (optional)</label>
            <input type="text" name="contact_phone" value="{{ $old('contact_phone') }}" class="field">
        </div>
    </div>
</div>
