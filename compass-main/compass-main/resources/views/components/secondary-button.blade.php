<button {{ $attributes->merge(['type' => 'button', 'class' => 'ui-btn ui-btn-muted']) }}>
    {{ $slot }}
</button>
