@props(['titulo', 'valor', 'icono', 'bgClass', 'textClass'])

<div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center justify-between">
    <div class="flex items-center gap-4">
        <div @class(['p-3 rounded-lg', $bgClass, $textClass])>
            <x-dynamic-component :component="'lucide-' . $icono" class="size-icon-xl" />
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $titulo }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $valor }}</p>
        </div>
    </div>
</div>
