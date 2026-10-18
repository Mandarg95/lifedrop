<?php

namespace App\Support;

class BloodGroup
{
    public const ALL = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    /** Tailwind classes used for badges and accents. */
    public const TONES = [
        'A+' => ['bg-rose-100 text-rose-700 ring-rose-200', 'bg-rose-500'],
        'A-' => ['bg-rose-100 text-rose-600 ring-rose-200', 'bg-rose-400'],
        'B+' => ['bg-amber-100 text-amber-700 ring-amber-200', 'bg-amber-500'],
        'B-' => ['bg-amber-100 text-amber-600 ring-amber-200', 'bg-amber-400'],
        'AB+' => ['bg-violet-100 text-violet-700 ring-violet-200', 'bg-violet-500'],
        'AB-' => ['bg-violet-100 text-violet-600 ring-violet-200', 'bg-violet-400'],
        'O+' => ['bg-sky-100 text-sky-700 ring-sky-200', 'bg-sky-500'],
        'O-' => ['bg-emerald-100 text-emerald-700 ring-emerald-200', 'bg-emerald-500'],
    ];

    public static function isValid(string $group): bool
    {
        return in_array($group, self::ALL, true);
    }

    public static function badge(string $group): string
    {
        return self::TONES[$group][0] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
    }

    public static function bar(string $group): string
    {
        return self::TONES[$group][1] ?? 'bg-slate-500';
    }
}
