@props(['width' => 'w-40'])

<div x-data="contextMenu" 
     class="contents"
     x-init="$watch('openMenu', value => value && $nextTick(() => calculatePosition($el.parentElement.querySelector('button'))))"
     @scroll.window.passive="if(openMenu) calculatePosition($el.parentElement.querySelector('button'))"
     @resize.window.passive="if(openMenu) calculatePosition($el.parentElement.querySelector('button'))">
    <template x-teleport="body">
        <div x-show="openMenu"
             x-ref="panel"
             @click.away="openMenu = false"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             style="display: none;"
             :style="`position: fixed; top: ${top}px; left: ${left}px; z-index: 50;`"
             {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-xl border border-gray-100/50 py-1.5 ' . $width]) }}>
            {{ $slot }}
        </div>
    </template>
</div>
