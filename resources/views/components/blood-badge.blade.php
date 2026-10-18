@props(['group'])

<span {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-md px-2 py-0.5 text-xs font-bold ring-1 ring-inset '.\App\Support\BloodGroup::badge($group)]) }}>
    {{ $group }}
</span>
