@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider']) }}>
    {{ $value ?? $slot }}
</label>
