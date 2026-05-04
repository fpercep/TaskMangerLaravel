@props(['color' => 'bg-gray-400'])

<button {{ $attributes->merge(['type' => 'button', 'class' => 'w-full text-left px-3 py-2 text-sidebar-item flex items-center transition-colors hover:bg-gray-50 text-gray-700 font-medium']) }}>
    <div class="w-2 h-2 rounded-full {{ $color }} shrink-0 mr-2.5"></div>
    {{ $slot }}
</button>
