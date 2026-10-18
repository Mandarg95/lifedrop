@extends('layouts.app')

@section('title', $donor->name)
@section('subtitle', 'Donor profile and donation history')

@section('actions')
    <a href="{{ route('donations.create', ['donor' => $donor->id]) }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        <span class="hidden sm:inline">Record donation</span>
    </a>
    <a href="{{ route('donors.edit', $donor) }}" class="btn-secondary hidden sm:inline-flex">Edit</a>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('donors.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-800">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to donors
        </a>
    </div>

    <section class="card overflow-hidden">
        <div class="h-20 bg-gradient-to-r from-rose-500 via-red-500 to-rose-600"></div>
        <div class="flex flex-col gap-4 px-5 pb-5 sm:flex-row sm:items-end sm:justify-between sm:px-6">
            <div class="-mt-10 flex items-end gap-4">
                <div class="grid size-20 place-items-center rounded-2xl border-4 border-white bg-slate-900 text-xl font-bold text-white shadow-lg">
                    {{ strtoupper(collect(preg_split('/\s+/', trim($donor->name)))->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->join('')) }}
                </div>
                <div class="pb-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ $donor->name }}</h2>
                        <x-blood-badge :group="$donor->blood_group" class="text-xs! px-2.5! py-1!" />
                        @if ($donor->isEligible())
                            <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Eligible now</span>
                        @elseif (! $donor->is_available)
                            <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-500 ring-1 ring-inset ring-slate-200">Unavailable</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">{{ $donor->eligibilityLabel() }}</span>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $donor->city ?: 'City not set' }} Â· {{ $donor->phone }}@if ($donor->email) Â· {{ $donor->email }}@endif
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('donors.destroy', $donor) }}"
                  data-confirm="Delete {{ $donor->name }} and all of their donation history? This cannot be undone.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </section>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card p-5">
            <p class="text-[13px] font-medium text-slate-500">Total donations</p>
            <p class="mt-1.5 text-2xl font-bold text-slate-900">{{ $donor->donations->count() }}</p>
        </div>
        <div class="card p-5">
            <p class="text-[13px] font-medium text-slate-500">Units donated</p>
            <p class="mt-1.5 text-2xl font-bold text-slate-900">{{ $unitsDonated }}</p>
        </div>
        <div class="card p-5">
            <p class="text-[13px] font-medium text-slate-500">Last donation</p>
            <p class="mt-1.5 text-2xl font-bold text-slate-900">
                {{ $donor->lastDonationDate() ? \Carbon\Carbon::parse($donor->lastDonationDate())->format('d M Y') : 'â€”' }}
            </p>
        </div>
        <div class="card p-5">
            <p class="text-[13px] font-medium text-slate-500">Next eligible</p>
            <p class="mt-1.5 text-2xl font-bold {{ $donor->isEligible() ? 'text-emerald-600' : 'text-amber-600' }}">
                {{ $donor->isEligible() ? 'Now' : (\Carbon\Carbon::parse($donor->nextEligibleDate())->format('d M Y')) }}
            </p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="card xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Donation history</h2>
                    <p class="text-xs text-slate-500">Every unit this donor has given</p>
                </div>
            </div>

            @if ($donor->donations->isEmpty())
                <div class="px-5 py-10 text-center text-sm text-slate-500">
                    No donations recorded yet.
                    <a href="{{ route('donations.create', ['donor' => $donor->id]) }}" class="ml-1 font-semibold text-rose-600 hover:text-rose-700">Record the first one</a>
                </div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($donor->donations as $donation)
                        <li class="flex items-center gap-4 px-5 py-4">
                            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-rose-50 text-rose-600">
                                <svg viewBox="0 0 24 24" fill="currentColor" class="size-4"><path d="M12 2.69s5.42 4.94 8.63 8.79A5.36 5.36 0 0 1 19.36 19a5.33 5.33 0 0 1-7.36-.3 5.33 5.33 0 0 1-7.36.3A5.36 5.36 0 0 1 3.37 11.5C6.58 7.63 12 2.69 12 2.69Z"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-800">{{ $donation->donation_date->format('d M Y') }}</p>
                                <p class="truncate text-xs text-slate-400">{{ $donation->camp ?: 'Blood bank drive' }}{{ $donation->notes ? ' Â· '.$donation->notes : '' }}</p>
                            </div>
                            <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">{{ $donation->units }} unit{{ $donation->units > 1 ? 's' : '' }}</span>
                            <form method="POST" action="{{ route('donations.destroy', $donation) }}"
                                  data-confirm="Delete this donation record? Donor eligibility will be recalculated.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ghost px-2! text-slate-400 hover:bg-rose-50 hover:text-rose-600" title="Delete record">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <aside class="space-y-6">
            <section class="card p-5">
                <h2 class="mb-4 text-sm font-bold text-slate-900">Details</h2>
                <dl class="space-y-3.5 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Blood group</dt>
                        <dd class="font-semibold text-slate-800">{{ $donor->blood_group }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Date of birth</dt>
                        <dd class="font-semibold text-slate-800">
                            {{ $donor->date_of_birth ? $donor->date_of_birth->format('d M Y').($donor->age ? ' ('.$donor->age.' yrs)' : '') : 'â€”' }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Gender</dt>
                        <dd class="font-semibold text-slate-800">{{ $donor->gender ? ucfirst($donor->gender) : 'â€”' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Weight</dt>
                        <dd class="font-semibold text-slate-800">{{ $donor->weight_kg ? number_format($donor->weight_kg, 1).' kg' : 'â€”' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Address</dt>
                        <dd class="text-right font-semibold text-slate-800">{{ $donor->address ?: 'â€”' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Available</dt>
                        <dd>
                            <span class="rounded-md px-2 py-0.5 text-xs font-semibold {{ $donor->is_available ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $donor->is_available ? 'Yes' : 'No' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="card p-5">
                <h2 class="mb-3 text-sm font-bold text-slate-900">Medical notes</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    {{ $donor->medical_notes ?: 'No medical notes on file.' }}
                </p>
            </section>
        </aside>
    </div>
@endsection
