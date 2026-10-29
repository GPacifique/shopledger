@props([
    // 'full' = icon + app name, 'mark' = icon only
    'variant'   => 'full',
    'name'      => 'MahWi',
    // Classes for the app name text (override to resize or recolor it)
    'nameClass' => 'text-xl font-bold tracking-tight text-[color:var(--text)]',
])

@php
    $showName = $variant !== 'mark';
@endphp

<span class="inline-flex items-center gap-2.5">
    <img
        src="{{ asset('icons/icon-192.png') }}"
        {{-- Decorative when the name is printed next to it, so screen readers don't read it twice --}}
        alt="{{ $showName ? '' : $name }}"
        {{ $attributes->merge([
            'class'     => 'h-10 w-auto object-contain select-none',
            'loading'   => 'eager',
            'decoding'  => 'async',
            'draggable' => 'false',
        ]) }}
    >

    @if($showName)
        <span class="{{ $nameClass }}">{{ $name }}</span>
    @endif
</span>