@extends('layouts.app')

@section('title', 'Donations')
@section('subtitle', 'Units collected over time')

@section('actions')
    <a href="{{ route('donations.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        <span class="hidden sm:inline">Record donation</span>
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <p class="text-[13px] font-medium text-slate-500">Total units collected</p>
            <p class="mt-1.5 text-3xl font-bold text-slate-900">{{ number_format($totalUnits) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-[13px] font-medium text-slate-500">Records shown</p>
            <p class="mt-1.5 text-3xl font-bold text-slate-900">{{ $donations->total() }}</p>
        </div>
        <div class="card p-5">
            <p class="text-[13px] font-medium text-slate-500">Last 30 days</p>
            <p class="mt-1.5 text-3xl font-bold text-rose-600">
                {{ \App\Models\Donation::where('donation_date', '>=', now()->subDays(30))->sum('units') }} units
            </p>
        </div>
    </div>

    <form method="GET" action="{{ route('donations.index') }}" class="card mt-6 p-4 sm:p-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
            <div class="flex-1">
                <label for="q" class="label">Search</label>
                <div class="relative">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400"><path stroke-linecap="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                    <input type="search" name="q" id="q" value="{{ $filters['q'] }}" data-focus-filter
                           placeholder="Donor name, phone or campaing" class="input pl-9">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:w-auto">
                <div>
                    <label for="blood_group" class="label">Blood group</label>
                    <select name="blood_group" id="blood_group" data-autosubmit class="input lg:w-36">
                        <option value="">All groups</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group }}" @selected($filters['blood_group'] === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="period" class="label">Period</label>
                    <select name="period" id="period" data-autosubmit class="input lg:w-40">
                        <option value="">All time</option>
                        <option value="30" @selected($filters['period'] === '30')>Last 30 days</option>
                        <option value="90" @selected($filters['period'] === '90')>Last 3 months</option>
                        <option value="365" @selected($filters['period'] === '365')>Last year</option>
                    </select>
                </div>
                <div class="col-span-2 flex items-end gap-2 sm:col-span-1">
                    <button type="submit" class="btn-secondary w-full">Filter</button>
                </div>
            </div>
        </div>

        @if (array_filter($filters))
            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                <span class="text-xs font-medium text-slate-400">Active filters</span>
                @foreach ($filters as $key => $value)
                    @if ($value)
                        <a href="{{ route('donations.index', \Illuminate\Support\Arr::except($filters, [$key])) }}" class="chip chip-active">
                            {{ $key === 'period' ? 'period: '.$value.' days' : $key.': '.$value }}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                @endforeach
                <span class="ml-auto text-xs text-slate-500">{{ $donations->total() }} result{{ $donations->total() === 1 ? '' : 's' }}</span>
            </div>
        @endif
    </form>

    @if ($donations->isEmpty())
        <div class="mt-6">
            <x-empty-state title="No donations found"
                           message="{{ array_filter($filters) ? 'No records match these filters.' : 'Record your first donation to start tracking the blood bank inventory.' }}">
                <x-slot:action>
                    @if (array_filter($filters))
                        <a href="{{ route('donations.index') }}" class="btn-secondary">Clear filters</a>
                    @else
                        <a href="{{ route('donations.create') }}" class="btn-primary">Record donation</a>
                    @endif
                </x-slot:action>
            </x-empty-state>
        </div>
    @else
        <div class="card mt-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="table-head">Donor</th>
                            <th class="table-head">Date</th>
                            <th class="table-head">Group</th>
                            <th class="table-head">Units</th>
                            <th class="table-head hidden md:table-cell">Camp / location</th>
                            <th class="table-head text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($donations as $donation)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="table-cell">
                                    <div class="flex items-center gap-3">
                                        <x-avatar :name="$donation->donor->name" :group="$donation->donor->blood_group" />
                                        <div class="min-w-0">
                                            <a href="{{ route('donors.show', $donation->donor) }}" class="block font-semibold text-slate-900 hover:text-rose-600">{{ $donation->donor->name }}</a>
                                            <p class="truncate text-xs text-slate-400">{{ $donation->donor->phone }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-cell font-medium text-slate-700">{{ $donation->donation_date->format('d M Y') }}</td>
                                <td class="table-cell"><x-blood-badge :group="$donation->donor->blood_group" /></td>
                                <td class="table-cell">
                                    <span class="rounded-md bg-rose-50 px-2 py-1 text-xs font-bold text-rose-700">{{ $donation->units }}</span>
                                </td>
                                <td class="table-cell hidden md:table-cell">{{ $donation->camp ?: 'â€”' }}</td>
                                <td class="table-cell text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('donations.create', ['donor' => $donation->donor_id]) }}" class="btn-ghost px-2!" title="Repeat donation for this donor">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('donations.destroy', $donation) }}"
                                              data-confirm="Delete the donation record from {{ $donation->donation_date->format('d M Y') }}? Eligibility will be recalculated.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost px-2! text-rose-500 hover:bg-rose-50 hover:text-rose-700" title="Delete">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 px-5 py-3.5">
                <p class="text-xs text-slate-500">Showing {{ $donations->firstItem() }}â€“{{ $donations->lastItem() }} of {{ $donations->total() }}</p>
                {{ $donations->links() }}
            </div>
        </div>
    @endif
@endsection
