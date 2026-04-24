@props(['name' => 'priority', 'value' => 1])

<div {{ $attributes->merge(['class' => 'py-4']) }} x-data="prioritySlider({{ $value }})">
    <div class="flex justify-between items-center mb-10">
        <x-ui.input-label :value="__('Prioridad')" class="!mb-0 text-gray-400 font-medium" />
        <div class="flex items-center gap-2 px-3 py-1 bg-gray-50 rounded-full border border-gray-100 transition-all duration-300">
             <div class="w-2 h-2 rounded-full shadow-sm" :class="current.color"></div>
             <span x-text="current.label" 
                   class="text-[11px] font-bold uppercase tracking-wider text-gray-700"></span>
        </div>
    </div>

    <div class="relative h-1 flex items-center mx-1">
        <!-- Track Background -->
        <div class="absolute w-full h-full bg-gray-100 rounded-full"></div>
        
        <!-- Active Track -->
        <div class="absolute h-full rounded-full transition-all duration-300 ease-out pointer-events-none"
             :class="current.color"
             :style="trackStyle"></div>
             
        <!-- Discrete Points -->
        <div class="absolute w-full flex justify-between items-center pointer-events-none">
            <template x-for="i in priorities.length">
                <div class="w-1.5 h-1.5 rounded-full bg-white/80 shadow-sm z-10"></div>
            </template>
        </div>
        
        <!-- Thumb -->
        <div class="absolute w-4 h-4 bg-white border-2 rounded-full shadow-md z-20 transition-all duration-300 ease-out pointer-events-none -translate-x-1/2"
             :style="thumbStyle"
             :class="current.borderColor">
        </div>

        <!-- Real Range Input -->
        <input type="range" 
               min="0" 
               :max="priorities.length - 1" 
               step="1"
               x-model="priorityIndex" 
               class="absolute inset-x-[-10px] w-[calc(100%+20px)] h-8 opacity-0 cursor-pointer z-30 appearance-none">
        
        <input type="hidden" :name="'{{ $name }}'" :value="current.name">
    </div>

    <!-- Labels below -->
    <div class="flex justify-between mt-6 px-0">
        <template x-for="(p, index) in priorities" :key="index">
            <button type="button" 
                    @click="priorityIndex = index"
                    class="text-[10px] font-bold uppercase tracking-[0.1em] transition-all duration-200 w-12 text-center focus:outline-none"
                    :class="priorityIndex == index ? 'text-gray-900 scale-110' : 'text-gray-300 hover:text-gray-400'"
                    x-text="p.label"></button>
        </template>
    </div>
</div>
