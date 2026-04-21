@props(['name', 'value' => null, 'label'])

<label class="flex items-center gap-3">
    <input type="radio" 
           name="{{ $name }}" 
           value="{{ $value }}" 
           {{ $attributes->merge(['class' => 'h-4 w-4 text-gray-900 border-gray-300 focus:ring-gray-900']) }}>
    <span class="text-sm text-gray-700">{{ $label }}</span>
</label>
