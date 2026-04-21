@props(['on' => false])

@if ($on)
    <p
        x-data="{ show: true }"
        x-show="show"
        x-transition
        x-init="setTimeout(() => show = false, 2000)"
        {{ $attributes->merge(['class' => 'text-sm text-green-600 flex items-center gap-1.5 bg-green-50 px-3 py-1.5 rounded-md border border-green-100']) }}
    >
        <x-lucide-check-circle-2 class="w-4 h-4 shrink-0"/>
        <span>{{ $slot }}</span>
    </p>
@endif
