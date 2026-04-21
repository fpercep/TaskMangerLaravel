@props(['default' => 'false', 'name' => ''])

<button 
    type="button" 
    x-data="{ state: {{ $default }} }" 
    @click="state = !state" 
    :class="state ? 'bg-orange-500' : 'bg-gray-200'" 
    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2" 
    role="switch">
    
    <input type="hidden" name="{{ $name }}" :value="state">

    <span 
        :class="state ? 'translate-x-5' : 'translate-x-0'" 
        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
    </span>
</button>
