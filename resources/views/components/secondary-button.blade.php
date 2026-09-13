<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-ghost btn-sm']) }}>
    {{ $slot }}
</button>
