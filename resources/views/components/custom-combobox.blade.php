@props([
    'id',
    'label',
    'optional' => false,
    'placeholder' => 'Buscar...',
    'icon' => 'search-outline',
    'emptyMessage' => 'Nenhum item encontrado.',
    'onSelect' => '', // JS callback
    'helperText' => '', // Text below the input
    'selectedValue' => '',
    'selectedName' => ''
])

<div class="mb-5 relative custom-combobox-container" id="combo-container-{{ $id }}">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }} 
    </label>
    
    <!-- Hidden input to store selected ID -->
    <input type="hidden" id="{{ $id }}" value="{{ $selectedValue }}" data-onselect="{{ $onSelect }}">

    <!-- Search Input -->
    <div class="relative">
        <input type="text" id="combo-search-{{ $id }}" autocomplete="off"
            placeholder="{{ $placeholder }}"
            value="{{ $selectedName }}"
            class="combo-search-input block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#D0AE6D] focus:ring-[#D0AE6D] pl-4 pr-11 py-2.5 text-sm cursor-text transition-colors"
        >
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
            <ion-icon name="{{ $icon }}" class="text-gray-400 text-lg"></ion-icon>
        </div>
    </div>

    <!-- Dropdown list -->
    <ul id="combo-dropdown-{{ $id }}" class="combo-dropdown-list absolute z-50 w-full bg-white border border-gray-200 shadow-xl max-h-60 rounded-lg py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden mt-1">
        @if($optional)
        <li class="combo-option cursor-pointer select-none relative py-2.5 pl-4 pr-4 hover:bg-gray-50 text-gray-900 border-b border-gray-100" data-value="">
            <span class="block truncate font-medium text-gray-400 italic">Nenhuma seleção</span>
        </li>
        @endif
        
        {{ $slot }}
        
        <li class="combo-empty hidden cursor-default select-none relative py-3 pl-4 pr-4 text-gray-500 text-sm text-center">
            {{ $emptyMessage }}
        </li>
    </ul>

    @if($helperText)
        <p class="text-[10px] text-gray-400 mt-1">{{ $helperText }}</p>
    @endif
</div>
