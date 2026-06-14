{{-- resources/views/components/form/field.blade.php --}}
@props(['name', 'label', 'required' => false])

<div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
    <label for="{{ $name }}" class="text-sm font-medium text-gray-700 sm:w-32 sm:flex-shrink-0">
        {{ $label }} @if($required) * @endif
    </label>
    <div class="w-full">
        {{ $slot }}
        @error($name)
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>