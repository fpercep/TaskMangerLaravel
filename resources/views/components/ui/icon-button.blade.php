@props(['icon', 'title' => '', 'href' => null, 'iconClass' => '', 'size' => 'sm'])

@php
    $sizeMap = [
        'xs' => 'size-icon-xs',
        'sm' => 'size-icon-sm',
        'md' => 'size-icon-md',
        'lg' => 'size-icon-lg',
        'xl' => 'size-icon-xl',
    ];
    
    $iconSizeClass = $sizeMap[$size] ?? $sizeMap['sm'];
    $classes = "flex justify-center items-center p-1.5 rounded-btn text-gray-400 hover:text-gray-800 hover:bg-gray-100 transition-colors focus:outline-none cursor-pointer";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'title' => $title]) }}>
        <x-dynamic-component :component="'lucide-' . $icon" class="{{ $iconSizeClass }} {{ $iconClass }}" />
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'title' => $title]) }}>
        <x-dynamic-component :component="'lucide-' . $icon" class="{{ $iconSizeClass }} {{ $iconClass }}" />
    </button>
@endif
