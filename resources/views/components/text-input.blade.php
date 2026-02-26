@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#D0AE6D] focus:ring-[#D0AE6D] rounded-md shadow-sm']) }}>