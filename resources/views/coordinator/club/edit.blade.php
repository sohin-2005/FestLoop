<x-coordinator-layout>
    <p class="eyebrow">Club page</p>
    <h1 class="headline text-3xl text-ink">Edit your public page</h1>

    <form method="POST" action="{{ route('coordinator.club.update') }}" enctype="multipart/form-data" class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        @csrf @method('PATCH')

        <div class="space-y-5 lg:col-span-2">
            <div class="panel p-6">
                <label class="field-label">Club name</label>
                <input type="text" name="name" value="{{ old('name', $club->name) }}" required class="field text-lg font-semibold">

                <div class="mt-5 grid grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Short name / abbreviation</label>
                        <input type="text" name="short_name" maxlength="12" value="{{ old('short_name', $club->short_name) }}" placeholder="e.g. BFC" class="field">
                    </div>
                    <div>
                        <label class="field-label">Category</label>
                        <select name="category" required class="field">
                            @foreach (\App\Models\Club::CATEGORIES as $value => $label)
                                <option value="{{ $value }}" @selected(old('category', $club->category) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <label class="field-label mt-5">Tagline</label>
                <input type="text" name="tagline" value="{{ old('tagline', $club->tagline) }}" placeholder="One punchy line" class="field">

                <label class="field-label mt-5">About</label>
                <textarea name="about" rows="5" class="field">{{ old('about', $club->about) }}</textarea>

                <label class="field-label mt-5">Mission statement</label>
                <textarea name="mission" rows="2" class="field">{{ old('mission', $club->mission) }}</textarea>
            </div>

            {{-- Achievements --}}
            <div class="panel p-6">
                <h2 class="font-display text-xl text-ink">🏆 Achievements</h2>
                <div class="mt-3 space-y-2">
                    @forelse ($club->achievements as $a)
                        <div class="flex items-start justify-between gap-3 rounded-lg border border-ink/10 p-3">
                            <div>
                                <p class="text-sm font-semibold text-ink">{{ $a->title }}</p>
                                <p class="text-xs text-ink-mute">{{ $a->achieved_on?->format('M Y') }}</p>
                            </div>
                            <form method="POST" action="{{ route('coordinator.club.achievements.destroy', $a) }}"><input type="hidden" name="_dummy">@csrf @method('DELETE')<button class="btn-quiet text-xs text-red-700">Remove</button></form>
                        </div>
                    @empty
                        <p class="text-sm text-ink-mute">No achievements added yet.</p>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('coordinator.club.achievements.store') }}" class="mt-4 space-y-2 border-t border-dashed border-ink/20 pt-4">
                    @csrf
                    <input type="text" name="title" placeholder="e.g. Best Club Award 2026" required class="field">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" name="achieved_on" class="field">
                        <input type="text" name="description" placeholder="Short description (optional)" class="field">
                    </div>
                    <button class="btn-ghost btn-sm">+ Add achievement</button>
                </form>
            </div>

            {{-- Roadmap --}}
            <div class="panel p-6">
                <h2 class="font-display text-xl text-ink">🧭 Roadmap</h2>
                <div class="mt-3 space-y-2">
                    @forelse ($club->roadmapItems as $r)
                        <div class="flex items-start justify-between gap-3 rounded-lg border border-ink/10 p-3">
                            <div>
                                <p class="text-sm font-semibold text-ink">{{ $r->title }}</p>
                                <p class="text-xs text-ink-mute">{{ $r->status_label }}@if($r->target_label) · {{ $r->target_label }}@endif</p>
                            </div>
                            <form method="POST" action="{{ route('coordinator.club.roadmap.destroy', $r) }}">@csrf @method('DELETE')<button class="btn-quiet text-xs text-red-700">Remove</button></form>
                        </div>
                    @empty
                        <p class="text-sm text-ink-mute">No roadmap items yet.</p>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('coordinator.club.roadmap.store') }}" class="mt-4 space-y-2 border-t border-dashed border-ink/20 pt-4">
                    @csrf
                    <input type="text" name="title" placeholder="e.g. Launch a mentorship track" required class="field">
                    <div class="grid grid-cols-2 gap-2">
                        <select name="status" class="field">
                            @foreach (\App\Models\RoadmapItem::STATUSES as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="target_label" placeholder="e.g. Next semester" class="field">
                    </div>
                    <button class="btn-ghost btn-sm">+ Add roadmap item</button>
                </form>
            </div>

            {{-- Announcements --}}
            <div class="panel p-6">
                <h2 class="font-display text-xl text-ink">📣 Announcements</h2>
                <div class="mt-3 space-y-2">
                    @forelse ($club->announcements as $an)
                        <div class="flex items-start justify-between gap-3 rounded-lg border border-ink/10 p-3">
                            <div>
                                <p class="text-sm font-semibold text-ink">{{ $an->title }} @if($an->is_pinned)<span class="text-tangerine">📌</span>@endif</p>
                                <p class="text-xs text-ink-mute">{{ $an->created_at->format('M d, Y') }}</p>
                            </div>
                            <form method="POST" action="{{ route('coordinator.club.announcements.destroy', $an) }}">@csrf @method('DELETE')<button class="btn-quiet text-xs text-red-700">Remove</button></form>
                        </div>
                    @empty
                        <p class="text-sm text-ink-mute">No announcements yet.</p>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('coordinator.club.announcements.store') }}" class="mt-4 space-y-2 border-t border-dashed border-ink/20 pt-4">
                    @csrf
                    <input type="text" name="title" placeholder="Title" required class="field">
                    <textarea name="body" rows="2" placeholder="What's the update?" required class="field"></textarea>
                    <label class="flex items-center gap-2 text-xs text-ink-soft"><input type="checkbox" name="is_pinned" value="1" class="rounded border-ink/30 text-tangerine"> Pin to top</label>
                    <button class="btn-ghost btn-sm">+ Post announcement</button>
                </form>
            </div>
        </div>

        {{-- sidebar: media + links, saved with main form --}}
        <div class="space-y-5">
            <div class="panel p-6">
                <label class="field-label">Logo</label>
                @if ($club->logo_url)<img src="{{ $club->logo_url }}" class="mb-2 h-20 w-20 rounded-xl border-2 border-ink object-cover">@endif
                <input type="file" name="logo_image" accept="image/*" class="field !py-2 text-xs">

                <label class="field-label mt-5">Cover image</label>
                @if ($club->cover_url)<img src="{{ $club->cover_url }}" class="mb-2 h-20 w-full rounded-xl border-2 border-ink object-cover">@endif
                <input type="file" name="cover_image" accept="image/*" class="field !py-2 text-xs">

                <label class="field-label mt-5">Accent color</label>
                <input type="color" name="accent_color" value="{{ old('accent_color', $club->accent_color) }}" class="h-11 w-full rounded-xl border-2 border-ink">
            </div>

            <div class="panel p-6">
                <p class="field-label">Details</p>
                <label class="field-label mt-3 !mb-1">Founded year</label>
                <input type="number" name="founded_year" value="{{ old('founded_year', $club->founded_year) }}" class="field">
                <label class="field-label mt-3 !mb-1">Faculty advisor</label>
                <input type="text" name="faculty_advisor" value="{{ old('faculty_advisor', $club->faculty_advisor) }}" class="field">
            </div>

            <div class="panel p-6">
                <p class="field-label">Contact & links</p>
                <label class="field-label mt-3 !mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $club->email) }}" class="field">
                <label class="field-label mt-3 !mb-1">Website</label>
                <input type="url" name="website" value="{{ old('website', $club->website) }}" class="field">
                <label class="field-label mt-3 !mb-1">Instagram handle</label>
                <input type="text" name="instagram" value="{{ old('instagram', $club->instagram) }}" placeholder="@handle" class="field">
                <label class="field-label mt-3 !mb-1">LinkedIn URL</label>
                <input type="url" name="linkedin" value="{{ old('linkedin', $club->linkedin) }}" class="field">
            </div>

            <button type="submit" class="btn-accent w-full">Save club page</button>
        </div>
    </form>
</x-coordinator-layout>
