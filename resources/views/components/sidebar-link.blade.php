@props(['active' => false, 'href'])

@php
$classes = ($active ?? false)
            ? 'block px-3 py-2 rounded bg-gray-200 dark:bg-gray-700 font-semibold'
            : 'block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700';
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => $classes]) }}>
    {{ $slot }}
</a>
