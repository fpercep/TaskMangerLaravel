@props(['icon', 'title' => '', 'href' => null, 'iconClass' => 'w-5 h-5'])

@php
    $classes = "flex justify-center items-center w-8 h-8 rounded-btn text-gray-400 hover:text-gray-800 hover:bg-gray-100 transition-colors focus:outline-none cursor-pointer";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'title' => $title]) }}>
        <i data-lucide="{{ $icon }}" class="{{ $iconClass }}"></i>
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'title' => $title]) }}>
        <i data-lucide="{{ $icon }}" class="{{ $iconClass }}"></i>
    </button>
@endif
