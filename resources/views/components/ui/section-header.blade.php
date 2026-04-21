@props(['title', 'description' => null, 'icon', 'iconColor' => 'gray'])

@php
    $bgColors = [
        'gray' => 'bg-gray-50 text-gray-500',
        'blue' => 'bg-blue-50 text-blue-500',
        'orange' => 'bg-orange-50 text-orange-500',
        'red' => 'bg-red-50 text-red-500',
    ];
    $colorClass = $bgColors[$iconColor] ?? $bgColors['gray'];
@endphp

<header {{ $attributes->merge(['class' => 'mb-6 flex items-center gap-3 border-b border-gray-50 pb-4']) }}>
    <div class="p-2 rounded-lg {{ $colorClass }} shrink-0">
        <x-dynamic-component :component="'lucide-' . $icon" class="w-5 h-5" />
    </div>
    <div>
        <h2 class="text-lg font-medium text-gray-900">{{ $title }}</h2>
        @if($description)
            <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
        @endif
    </div>
</header>
