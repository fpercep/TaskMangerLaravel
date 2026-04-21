@props(['name', 'maxWidth' => 'md', 'centered' => false, 'alpineData' => '{}'])

@php
$maxWidthClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
][$maxWidth] ?? 'max-w-md';

$alignmentClass = $centered ? 'text-center' : '';
@endphp

<div x-data="modalState('{{ $name }}', {{ $alpineData }})" 
     @open-modal.window="handleOpen($event)"
     @close-modal.window="handleClose($event)"
     x-show="show" 
     style="display: none;"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">

    <!-- Fondo desenfocado (Backdrop unificado) -->
    <div x-show="show" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="show = false"
         class="fixed inset-0 bg-gray-900/30 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Card (Estética unificada) -->
    <div x-show="show" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="relative bg-white rounded-dropdown shadow-[0_10px_35px_rgb(0,0,0,0.06)] border border-gray-100 w-full {{ $maxWidthClasses }} {{ $alignmentClass }} transform transition-all overflow-hidden z-10">
        
        {{ $slot }}

    </div>
</div>
