@php
    $locales = [
        'en' => ['name' => 'English', 'flag' => '🇬🇧'],
        'fr' => ['name' => 'Français', 'flag' => '🇫🇷'],
        'rw' => ['name' => 'Kinyarwanda', 'flag' => '🇷🇼'],
        'sw' => ['name' => 'Kiswahili', 'flag' => '🇹🇿'],
    ];
    $currentLocale = app()->getLocale();
@endphp

<div x-data="{ open: false }" class="relative">
    <button
        @click="open = !open"
        @click.outside="open = false"
        type="button"
        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150"
    >
        <span class="mr-2">{{ $locales[$currentLocale]['flag'] }}</span>
        <span class="hidden sm:inline">{{ $locales[$currentLocale]['name'] }}</span>
        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        style="display: none;"
    >
        <div class="py-1">
            @foreach($locales as $code => $locale)
                <a
                    href="{{ route('language.switch', $code) }}"
                    class="flex items-center px-4 py-2 text-sm {{ $currentLocale === $code ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <span class="mr-3 text-lg">{{ $locale['flag'] }}</span>
                    <span>{{ $locale['name'] }}</span>
                    @if($currentLocale === $code)
                        <svg class="ml-auto h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
