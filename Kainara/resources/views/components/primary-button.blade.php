<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#B39C59] hover:bg-[#AD9D6D] border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
