@props(['projectName' => null, 'alpineProjectName' => null])

<div {{ $attributes->merge(['class' => 'px-5 py-2 border-b border-gray-100 flex items-center justify-between bg-gray-50/30']) }}>
    <div class="flex items-center gap-2 text-xs uppercase tracking-widest font-medium text-gray-400">
        <x-lucide-folder class="w-4 h-4" />
        @if($alpineProjectName)
            <span x-text="{{ $alpineProjectName }}"></span>
        @elseif($projectName)
            <span>{{ $projectName }}</span>
        @else
            {{ $slot }}
        @endif
    </div>

    <button @click="show = false" type="button"
        class="text-gray-400 hover:text-gray-600 transition-colors p-1.5 rounded-lg hover:bg-gray-100 focus:outline-none">
        <x-lucide-x class="w-5 h-5" />
    </button>
</div>
