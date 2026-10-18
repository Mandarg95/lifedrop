@extends('layouts.app')

@section('title', 'Donors')
@section('subtitle', 'Search, filter and manage the donor registry')

@section('actions')
    <a href="{{ route('donors.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        <span class="hidden sm:inline">Add donor</span>
    </a>
@endsection

@section('content')
    <form method="GET" action="{{ route('donors.index') }}" class="card p-4 sm:p-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
            <div class="flex-1">
                <label for="q" class="label">Search</label>
                <div class="relative">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400"><path stroke-linecap="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                    <input type="search" name="q" id="q" value="{{ $filters['q'] }}" data-focus-filter
                           placeholder="Name, phone, city or emailâ€¦" class="input pl-9">
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
                    <label for="availability" class="label">Availability</label>
                    <select name="availability" id="availability" data-autosubmit class="input lg:w-44">
                        <option value="">Any status</option>
                        <option value="eligible" @selected($filters['availability'] === 'eligible')>Eligible now</option>
                        <option value="deferring" @selected($filters['availability'] === 'deferring')>In deferral</option>
                        <option value="unavailable" @selected($filters['availability'] === 'unavailable')>Unavailable</option>
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
                        <a href="{{ route('donors.index', \Illuminate\Support\Arr::except($filters, [$key])) }}" class="chip chip-active">
                            {{ str_replace('_', ' ', $key) }}: {{ $value }}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                @endforeach
                <span class="ml-auto text-xs text-slate-500">{{ $donors->total() }} result{{ $donors->total() === 1 ? '' : 's' }}</span>
            </div>
        @endif
    </form>

    @if ($donors->isEmpty())
        <div class="mt-6">
            <x-empty-state title="No donors found"
                           message="{{ array_filter($filters) ? 'Try adjusting your filters to find what you are looking for.' : 'Start building your registry by adding your first donor.' }}">
                <x-slot:action>
                    @if (array_filter($filters))
                        <a href="{{ route('donors.index') }}" class="btn-secondary">Clear filters</a>
                    @else
                        <a href="{{ route('donors.create') }}" class="btn-primary">Add donor</a>
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
                            <th class="table-head">Blood group</th>
                            <th class="table-head hidden md:table-cell">Contact</th>
                            <th class="table-head hidden lg:table-cell">Last donation</th>
                            <th class="table-head">Status</th>
                            <th class="table-head text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($donors as $donor)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="table-cell">
                                    <div class="flex items-center gap-3">
                                        <x-avatar :name="$donor->name" :group="$donor->blood_group" />
                                        <div class="min-w-0">
                                            <a href="{{ route('donors.show', $donor) }}" class="block font-semibold text-slate-900 hover:text-rose-600">{{ $donor->name }}</a>
                                            <p class="truncate text-xs text-slate-400">{{ $donor->email ?: $donor->city ?: 'â€”' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-cell"><x-blood-badge :group="$donor->blood_group" /></td>
                                <td class="table-cell hidden md:table-cell">
                                    <p class="font-medium text-slate-700">{{ $donor->phone }}</p>
                                    <p class="text-xs text-slate-400">{{ $donor->city ?: 'City not set' }}</p>
                                </td>
                                <td class="table-cell hidden lg:table-cell">
                                    @if ($donor->last_donation_date)
                                        <p class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($donor->last_donation_date)->format('d M Y') }}</p>
                                        <p class="text-xs text-slate-400">{{ $donor->donations_count }} donation{{ $donor->donations_count === 1 ? '' : 's' }}</p>
                                    @else
                                        <span class="text-slate-400">Never donated</span>
                                    @endif
                                </td>
                                <td class="table-cell">
                                    @if ($donor->isEligible())
                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span> Eligible now
                                        </span>
                                    @elseif (! $donor->is_available)
                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-500 ring-1 ring-inset ring-slate-200">
                                            <span class="size-1.5 rounded-full bg-slate-400"></span> Unavailable
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">
                                            <span class="size-1.5 rounded-full bg-amber-500"></span> {{ $donor->eligibilityLabel() }}
                                        </span>
                                    @endif
                                </td>
                                <td class="table-cell text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('donors.show', $donor) }}" class="btn-ghost px-2!" title="View">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                        </a>
                                        <a href="{{ route('donors.edit', $donor) }}" class="btn-ghost px-2!" title="Edit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('donors.destroy', $donor) }}"
                                              data-confirm="Delete {{ $donor->name }} and all of their donation history? This cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost px-2! text-rose-500 hover:bg-rose-50 hover:text-rose-700" title="Delete">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 4.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
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
                <p class="text-xs text-slate-500">
                    Showing {{ $donors->firstItem() }}â€“{{ $donors->lastItem() }} of {{ $donors->total() }}
                </p>
                {{ $donors->links() }}
            </div>
        </div>
    @endif
@endsection
