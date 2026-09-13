<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <p class="eyebrow">Admin</p>
        <h1 class="headline text-3xl text-ink">Club approvals</h1>

        <h2 class="mt-8 font-display text-xl text-ink">Pending review ({{ $pending->count() }})</h2>
        @if ($pending->isEmpty())
            <p class="mt-3 text-sm text-ink-soft">Nothing waiting — you're all caught up.</p>
        @else
            <div class="mt-4 space-y-3">
                @foreach ($pending as $club)
                    <div class="panel flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-display text-lg text-ink">{{ $club->name }}</p>
                            <p class="text-xs text-ink-soft">{{ $club->category_label }} · {{ $club->tagline }} · registered {{ $club->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.clubs.approve', $club) }}"><input type="hidden">@csrf<button class="btn-accent btn-sm">Approve</button></form>
                            <form method="POST" action="{{ route('admin.clubs.reject', $club) }}" onsubmit="return confirm('Reject this club?')">@csrf<button class="btn-danger btn-sm">Reject</button></form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <h2 class="mt-10 font-display text-xl text-ink">Live clubs ({{ $approved->count() }})</h2>
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach ($approved as $club)
                <a href="{{ route('clubs.show', $club) }}" class="panel-soft flex items-center justify-between p-4 hover:border-ink">
                    <div>
                        <p class="font-semibold text-ink">{{ $club->name }}</p>
                        <p class="text-xs text-ink-soft">{{ $club->events_count }} events · {{ $club->followers_count }} followers</p>
                    </div>
                    <span class="chip !border-ink/15">{{ $club->category_label }}</span>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
