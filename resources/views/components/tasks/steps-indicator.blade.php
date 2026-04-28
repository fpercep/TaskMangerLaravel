@props(['completed', 'total'])

<div {{ $attributes->merge(['class' => 'flex items-center gap-1 ml-0.5']) }}>
    <div class="h-2.5 w-px bg-current opacity-40"></div>
    <x-lucide-check-circle-2 class="size-3" stroke-width="1.5" />
    <span x-text="`${ {{ $completed }} }/${ {{ $total }} }`"></span>
</div>
