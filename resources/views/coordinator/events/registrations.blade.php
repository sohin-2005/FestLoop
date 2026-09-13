<x-coordinator-layout>
    <a href="{{ route('coordinator.events.index') }}" class="btn-quiet text-xs">← Back to events</a>
    <p class="eyebrow mt-3">Sign-ups</p>
    <h1 class="headline text-3xl text-ink">{{ $event->name }}</h1>

    @if ($registrations->isEmpty())
        <div class="panel-soft mt-6 py-14 text-center text-ink-soft">No one has registered yet.</div>
    @else
        <div class="mt-6 overflow-x-auto rounded-2xl border-2 border-ink">
            <table class="w-full min-w-[560px] text-left text-sm">
                <thead class="border-b-2 border-ink bg-ink text-cream">
                    <tr>
                        <th class="px-4 py-3 font-mono text-[11px] uppercase tracking-wider">Student</th>
                        <th class="px-4 py-3 font-mono text-[11px] uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 font-mono text-[11px] uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/10 bg-cream">
                    @foreach ($registrations as $registration)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-ink">{{ $registration->user->name }}</td>
                            <td class="px-4 py-3 text-ink-soft">{{ $registration->user->email }}</td>
                            <td class="px-4 py-3"><span class="chip !border-ink/15">{{ $registration->label }}</span></td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('coordinator.events.registrations.update', [$event, $registration]) }}" class="inline-flex gap-1">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="chip cursor-pointer border-ink/25 bg-cream pr-6 text-xs">
                                        @foreach (\App\Models\Registration::LABELS as $value => $label)
                                            <option value="{{ $value }}" @selected($registration->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-coordinator-layout>
