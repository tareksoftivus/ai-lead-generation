@props([
    'entrypoints',
])

@php
    $viteTags = app(\Illuminate\Foundation\Vite::class)($entrypoints);
@endphp
<?php echo str_replace(' />', '>', (string) $viteTags); ?>
