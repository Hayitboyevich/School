<button {{ $attributes->merge(['type' => 'button', 'class' => 'flex justify-center items-center gap-2 bg-gray-100 py-2 px-3 rounded-lg mx-auto']) }}>
    {{ $slot }}
</button>
