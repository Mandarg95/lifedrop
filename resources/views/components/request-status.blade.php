@props(['status'])

@php
    $styles = [
        'pending' => 'bg-amber-100 text-amber-700 ring-amber-200',
        'fulfilled' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
        'cancelled' => 'bg-slate-100 text-slate-500 ring-slate-200',
    ];
    $label = ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset '.($styles[$status] ?? $styles['cancelled'])]) }}>
    <span class="size-1.5 rounded-full bg-current opacity-70"></span>
    {{ $label }}
</span>
