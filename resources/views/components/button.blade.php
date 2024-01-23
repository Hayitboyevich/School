<button {{ $attributes->merge(['type' => 'submit', 'class' => 'flex w-full justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg']) }}>
    {{ $slot }}
</button>
