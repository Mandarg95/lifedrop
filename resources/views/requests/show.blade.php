@extends('layouts.app')

@section('title', $bloodRequest->patient_name)
@section('subtitle', 'Blood request details')

@section('actions')
    <a href="{{ route('requests.edit', $bloodRequest) }}" class="btn-secondary hidden sm:inline-flex">Edit</a>
    @if ($bloodRequest->status === 'pending')
        <form method="POST" action="{{ route('requests.fulfill', $bloodRequest) }}"
              data-confirm="Mark this request as fulfilled? Units fulfilled will be set to {{ $bloodRequest->units_needed }}.">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn-primary">Mark fulfilled</button>
        </form>
    @endif
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('requests.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-800">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to requests
        </a>
    </div>

    <section class="card overflow-hidden">
        <div class="flex flex-col gap-5 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
            <div class="flex items-center gap-4">
                <span @class([
                    'grid size-14 shrink-0 place-items-center rounded-2xl text-lg font-bold text-white shadow-sm',
                    'bg-gradient-to-br from-rose-500 to-red-600' => $bloodRequest->urgency === 'critical',
                    'bg-gradient-to-br from-amber-400 to-amber-600' => $bloodRequest->urgency === 'urgent',
                    'bg-gradient-to-br from-slate-400 to-slate-600' => $bloodRequest->urgency === 'routine',
                ])>
                    {{ $bloodRequest->blood_group }}
                </span>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ $bloodRequest->patient_name }}</h2>
                        <x-request-status :status="$bloodRequest->status" />
                        <span @class([
                            'rounded-md px-2 py-1 text-xs font-bold',
                            'bg-rose-50 text-rose-700' => $bloodRequest->urgency === 'critical',
                            'bg-amber-50 text-amber-700' => $bloodRequest->urgency === 'urgent',
                            'bg-slate-100 text-slate-500' => $bloodRequest->urgency === 'routine',
                        ])>{{ ucfirst($bloodRequest->urgency) }}</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $bloodRequest->hospital }} · {{ $bloodRequest->units_needed }} unit{{ $bloodRequest->units_needed > 1 ? 's' : '' }} needed
                        @if ($bloodRequest->needed_by) · by {{ $bloodRequest->needed_by->format('d M Y') }} @endif
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                @if ($bloodRequest->status === 'pending')
                    <form method="POST" action="{{ route('requests.cancel', $bloodRequest) }}" data-confirm="Cancel this request?">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-secondary">Cancel request</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('requests.destroy', $bloodRequest) }}"
                      data-confirm="Delete the request for {{ $bloodRequest->patient_name }}?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Delete</button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 p-5 sm:p-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-[13px] font-medium text-slate-500">Fulfilment progress</p>
                        <p class="mt-1 text-3xl font-bold text-slate-900">
                            {{ $bloodRequest->units_fulfilled }}
                            <span class="text-lg font-semibold text-slate-400">/ {{ $bloodRequest->units_needed }} units</span>
                        </p>
                    </div>
                    <span class="text-sm font-bold {{ $bloodRequest->progress >= 100 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $bloodRequest->progress }}%</span>
                </div>

                <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full transition-all duration-700 {{ $bloodRequest->progress >= 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-rose-500 to-red-500' }}"
                         style="width: {{ max(3, $bloodRequest->progress) }}%"></div>
                </div>

                @if ($bloodRequest->is_overdue)
                    <div class="mt-4 flex gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mt-0.5 size-5 shrink-0 text-rose-500"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        <p><span class="font-semibold">Past the deadline.</span> This request was needed by {{ $bloodRequest->needed_by->format('d M Y') }}.</p>
                    </div>
                @endif

                <div class="mt-6">
                    <h3 class="text-sm font-bold text-slate-900">Notes</h3>
                    <p class="mt-2 rounded-xl bg-slate-50 p-4 text-sm leading-relaxed text-slate-600">
                        {{ $bloodRequest->notes ?: 'No additional notes.' }}
                    </p>
                </div>
            </div>

            <aside class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h3 class="text-sm font-bold text-slate-900">Contact</h3>
                <dl class="mt-4 space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Contact person</dt>
                        <dd class="mt-1 font-semibold text-slate-800">{{ $bloodRequest->contact_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Phone</dt>
                        <dd class="mt-1">
                            <a href="tel:{{ $bloodRequest->contact_phone }}" class="font-semibold text-rose-600 hover:text-rose-700">{{ $bloodRequest->contact_phone }}</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Hospital</dt>
                        <dd class="mt-1 font-semibold text-slate-800">{{ $bloodRequest->hospital }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Needed by</dt>
                        <dd class="mt-1 font-semibold text-slate-800">
                            {{ $bloodRequest->needed_by ? $bloodRequest->needed_by->format('d M Y') : 'No deadline set' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Raised</dt>
                        <dd class="mt-1 font-semibold text-slate-800">{{ $bloodRequest->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                </dl>

                <div class="mt-5 border-t border-slate-200 pt-4">
                    <a href="{{ route('donors.index', ['blood_group' => $bloodRequest->blood_group, 'availability' => 'eligible']) }}"
                       class="btn-secondary w-full">
                        Find matching {{ $bloodRequest->blood_group }} donors
                    </a>
                </div>
            </aside>
        </div>
    </section>
@endsection
