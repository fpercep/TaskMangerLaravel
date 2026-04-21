@props(['name', 'icon'])

<button 
    @click="switchTab('{{ $name }}')"
    :class="{ 'bg-orange-50 text-orange-600': tab === '{{ $name }}', 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': tab !== '{{ $name }}' }"
    class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors w-full text-left focus:outline-none"
>
    <!-- Alpine controla dinámicamente las clases del componente del icono compilado -->
    <x-dynamic-component :component="'lucide-' . $icon" class="w-5 h-5 shrink-0 transition-colors" x-bind:class="{ 'text-orange-500': tab === '{{ $name }}', 'text-gray-400': tab !== '{{ $name }}' }" />
    <span class="truncate">{{ $slot }}</span>
</button>
