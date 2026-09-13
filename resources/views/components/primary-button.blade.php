<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-ink btn-sm']) }}>
    {{ $slot }}
</button>
