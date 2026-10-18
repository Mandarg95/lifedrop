@props(['name', 'group' => null])

@php
    $initials = collect(preg_split('/\s+/', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->join('');
    $tone = $group ? \App\Support\BloodGroup::badge($group) : 'bg-slate-100 text-slate-600 ring-slate-200';
@endphp

<span {{ $attributes->merge(['class' => 'grid size-9 shrink-0 place-items-center rounded-full text-xs font-bold ring-1 ring-inset '.$tone]) }}>
    {{ $initials ?: '?' }}
</span>
