<x-app-layout>
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <p class="eyebrow">Inbox</p>
        <h1 class="headline text-3xl text-ink">Notifications</h1>

        @if ($notifications->isEmpty())
            <div class="panel-soft mt-6 py-16 text-center text-ink-soft">You're all caught up — nothing here yet.</div>
        @else
            <div class="mt-6 space-y-2">
                @foreach ($notifications as $n)
                    <a href="{{ route('notifications.read', $n->id) }}" class="panel-soft flex items-start gap-3 p-4 {{ $n->read_at ? '' : 'border-ink bg-butter/10' }}">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full {{ $n->read_at ? 'bg-transparent' : 'bg-tangerine' }}"></span>
                        <div>
                            <p class="text-sm text-ink">{{ $n->data['message'] ?? 'Update' }}</p>
                            <p class="mt-1 font-mono text-[11px] text-ink-mute">{{ $n->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $notifications->links() }}</div>
        @endif
    </div>
</x-app-layout>
