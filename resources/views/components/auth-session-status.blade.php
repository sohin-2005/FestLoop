@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl border-2 border-moss bg-moss/10 p-3 text-sm font-medium text-moss']) }}>
        {{ $status }}
    </div>
@endif
