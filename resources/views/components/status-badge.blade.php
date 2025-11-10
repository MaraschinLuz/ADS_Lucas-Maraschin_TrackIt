@props([
    'value' => null,
    'size' => 'md', // md | sm
])

@php
    $status = strtolower((string) $value);
    $map = [
        'aberto'        => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
        'em andamento'  => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'resolvido'     => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
        'fechado'       => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
    ];
    $classes = $map[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    $pad = $size === 'sm' ? 'px-2 text-xs' : 'px-2.5 text-sm';
@endphp

<span {{ $attributes->merge(['class' => "$pad inline-flex leading-5 font-semibold rounded-full $classes"]) }}>
    {{ ucfirst($value ?? 'Não definido') }}
    </span>

