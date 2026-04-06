@props(['icon', 'href' => '#', 'textClass' => 'text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'method' => 'GET', 'action' => '#'])

@php
    $classes = "flex items-center gap-3 w-full py-2.5 px-3 text-menu font-medium rounded-menu transition-colors focus:outline-none " . $textClass;
@endphp

@if(strtoupper($method) === 'POST')
    <form method="POST" action="{{ $action }}" class="m-0 mt-1">
        @csrf
        <button type="submit" class="{{ $classes }}">
            <i data-lucide="{{ $icon }}" class="w-[17px] h-[17px]"></i>
            {{ $slot }}
        </button>
    </form>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        <i data-lucide="{{ $icon }}" class="w-[17px] h-[17px] shrink-0 text-gray-400"></i>
        {{ $slot }}
    </a>
@endif
