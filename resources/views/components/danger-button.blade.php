<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-danger btn-sm']) }}>
    {{ $slot }}
</button>
