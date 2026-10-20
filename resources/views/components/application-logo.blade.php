@props([
    // 'full' = icon + "MahWi" wordmark, 'mark' = bag icon only
    'variant' => 'full',
    'alt'     => 'MahWi',
])

@php
    $src = $variant === 'mark'
        ? asset('icons/icon-192.png')
        : asset('images/MAHWILOGO.png');
@endphp

<img
    src="{{ $src }}"
    alt="{{ $alt }}"
    {{ $attributes->merge([
        'class'    => 'h-10 w-auto object-contain select-none',
        'loading'  => 'eager',
        'decoding' => 'async',
        'draggable' => 'false',
    ]) }}
>