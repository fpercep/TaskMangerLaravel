@props(['options' => [], 'model' => 'value'])

<div class="relative flex-1" x-data="{ open: false }">
    {{-- Botón Trigger --}}
    <button type="button" @click="open = !open" @click.outside="open = false"
        class="w-full bg-gray-50 border border-transparent hover:bg-gray-100 text-gray-800 rounded-md px-3 py-1.5 text-sm focus:outline-none font-medium flex items-center justify-between transition-colors">
        <div class="flex items-center gap-2.5">
            <div class="w-2 h-2 rounded-full shrink-0"
                 :class="({{ json_encode($options) }}).find(o => o.value === {{ $model }})?.color"></div>
            <span x-text="({{ json_encode($options) }}).find(o => o.value === {{ $model }})?.label"></span>
        </div>
        <x-lucide-chevron-down class="w-3.5 h-3.5 text-gray-500" />
    </button>

    {{-- Panel Desplegable --}}
    <div x-show="open"
        class="absolute top-full left-0 mt-1.5 w-full bg-white rounded-lg shadow-xl border border-gray-100 py-1.5 z-20"
        style="display: none;"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">
        
        {{ $slot }}
    </div>
</div>
