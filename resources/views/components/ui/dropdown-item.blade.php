@props(['icon' => null, 'destructive' => false])

@php
    $baseClasses = "w-full text-left px-3 py-1.5 text-xs flex items-center transition-colors focus:outline-none rounded-sm";
    $colorClasses = $destructive 
        ? "text-rose-600 hover:bg-rose-50" 
        : "text-gray-600 hover:bg-gray-50 hover:text-gray-900";
    
    $classes = $baseClasses . ' ' . $colorClasses;
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
    @if($icon)
        <x-dynamic-component :component="'lucide-' . $icon" class="size-icon-xs mr-2 {{ $destructive ? 'text-rose-400' : 'text-gray-400' }}" />
    @endif
    {{ $slot }}
</button>
