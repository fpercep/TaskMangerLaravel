@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full text-sm text-gray-900 placeholder:text-gray-400 bg-gray-50 border border-transparent focus:border-orange-300 focus:bg-white focus:ring-2 focus:ring-orange-100 rounded-md p-3 transition-colors outline-none shadow-[inset_0_1px_2px_rgba(0,0,0,0.02)]']) }}>
