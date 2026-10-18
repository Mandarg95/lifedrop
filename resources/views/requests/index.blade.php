@extends('layouts.app')

@section('title', 'Blood Requests')
@section('subtitle', 'Patient requests and fulfilment tracking')

@section('actions')
    <a href="{{ route('requests.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        <span class="hidden sm:inline">New request</span>
    </a>
@endsection

@section('content')
    <div class="flex flex-wrap items-center gap-2">
        @php
            $tabs = [
                '' => ['All', null],
                'pending' => ['Pending', $counts['pending']],
                'fulfilled' => ['Fulfilled', $counts['fulfilled']],
                'cancelled' => ['Cancelled', $counts['cancelled']],
            ];
        @endphp
        @foreach ($tabs as $value => [$label, $count])
            <a href="{{ route('requests.index', array_filter(array_merge($filters, ['status' => $value]), fn ($v) => $v !== null && $v !== '')) }}"
               @class(['chip px-3! py-2!', 'chip-active' => ($filters['status'] ?? '') === $value])>
                {{ $label }}
                @if ($count !== null)
                    <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-500 @if (($filters['status'] ?? '') === $value) bg-white/70 text-rose-700 @endif">{{ $count }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('requests.index') }}" class="card mt-4 p-4 sm:p-5">
        <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
            <div class="flex-1">
                <label for="q" class="label">Search</label>
                <div class="relative">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400"><path stroke-linecap="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                    <input type="search" name="q" id="q" value="{{ $filters['q'] }}" data-focus-filter
                           placeholder="Patient, hospital or phoneâ€¦" class="input pl-9">
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
                    <label for="urgency" class="label">Urgency</label>
                    <select name="urgency" id="urgency" data-autosubmit class="input lg:w-40">
                        <option value="">Any urgency</option>
                        @foreach (['routine', 'urgent', 'critical'] as $level)
                            <option value="{{ $level }}" @selected($filters['urgency'] === $level)>{{ ucfirst($level) }}</option>
                        @endforeach
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
                        <a href="{{ route('requests.index', \Illuminate\Support\Arr::except($filters, [$key])) }}" class="chip chip-active">
                            {{ str_replace('_', ' ', $key) }}: {{ $value }}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                @endforeach
                <span class="ml-auto text-xs text-slate-500">{{ $requests->total() }} result{{ $requests->total() === 1 ? '' : 's' }}</span>
            </div>
        @endif
    </form>

    @if ($requests->isEmpty())
        <div class="mt-6">
            <x-empty-state title="No requests found"
                           message="{{ array_filter($filters) ? 'Nothing matches the current filters.' : 'There are no blood requests yet. Create one when a patient needs blood.' }}">
                <x-slot:action>
                    @if (array_filter($filters))
                        <a href="{{ route('requests.index') }}" class="btn-secondary">Clear filters</a>
                    @else
                        <a href="{{ route('requests.create') }}" class="btn-primary">New request</a>
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
                            <th class="table-head">Patient</th>
                            <th class="table-head">Blood group</th>
                            <th class="table-head hidden md:table-cell">Needed by</th>
                            <th class="table-head">Progress</th>
                            <th class="table-head">Status</th>
                            <th class="table-head text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($requests as $request)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="table-cell">
                                    <div class="flex items-center gap-3">
                                        <span @class([
                                            'grid size-9 shrink-0 place-items-center rounded-full',
                                            'bg-rose-50 text-rose-600' => $request->urgency === 'critical',
                                            'bg-amber-50 text-amber-600' => $request->urgency === 'urgent',
                                            'bg-slate-100 text-slate-500' => $request->urgency === 'routine',
                                        ])>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                                        </span>
                                        <div class="min-w-0">
                                            <a href="{{ route('requests.show', $request) }}" class="block font-semibold text-slate-900 hover:text-rose-600">{{ $request->patient_name }}</a>
                                            <p class="truncate text-xs text-slate-400">{{ $request->hospital }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-cell">
                                    <x-blood-badge :group="$request->blood_group" />
                                    <p class="mt-1 text-xs font-medium text-slate-400">{{ $request->units_fulfilled }}/{{ $request->units_needed }} units</p>
                                </td>
                                <td class="table-cell hidden md:table-cell">
                                    @if ($request->needed_by)
                                        <p class="font-medium {{ $request->is_overdue ? 'text-rose-600' : 'text-slate-700' }}">
                                            {{ $request->needed_by->format('d M Y') }}
                                        </p>
                                        <p class="text-xs {{ $request->is_overdue ? 'font-semibold text-rose-500' : 'text-slate-400' }}">
                                            {{ $request->is_overdue ? 'Overdue' : $request->needed_by->diffForHumans() }}
                                        </p>
                                    @else
                                        <span class="text-slate-400">No deadline</span>
                                    @endif
                                </td>
                                <td class="table-cell">
                                    <div class="w-32">
                                        <div class="mb-1 flex justify-between text-xs font-medium text-slate-500">
                                            <span>{{ $request->units_fulfilled }}/{{ $request->units_needed }}u</span>
                                            <span>{{ $request->progress }}%</span>
                                        </div>
                                        <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full {{ $request->progress >= 100 ? 'bg-emerald-500' : ($request->status === 'cancelled' ? 'bg-slate-300' : 'bg-rose-500') }}"
                                                 style="width: {{ max(4, $request->progress) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-cell">
                                    <x-request-status :status="$request->status" />
                                    <p class="mt-1 text-xs font-medium {{ $request->urgency === 'critical' ? 'text-rose-600' : ($request->urgency === 'urgent' ? 'text-amber-600' : 'text-slate-400') }}">
                                        {{ ucfirst($request->urgency) }}
                                    </p>
                                </td>
                                <td class="table-cell text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('requests.show', $request) }}" class="btn-ghost px-2!" title="View">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                        </a>
                                        <a href="{{ route('requests.edit', $request) }}" class="btn-ghost px-2!" title="Edit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('requests.destroy', $request) }}"
                                              data-confirm="Delete the request for {{ $request->patient_name }}?">
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
                <p class="text-xs text-slate-500">Showing {{ $requests->firstItem() }}â€“{{ $requests->lastItem() }} of {{ $requests->total() }}</p>
                {{ $requests->links() }}
            </div>
        </div>
    @endif
@endsection
