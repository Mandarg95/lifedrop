@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Blood inventory, donor activity and open requests')

@section('actions')
    <a href="{{ route('donations.create') }}" class="btn-secondary hidden sm:inline-flex">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Record donation
    </a>
    <a href="{{ route('requests.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        New request
    </a>
@endsection

@section('content')
    @php
        $cards = [
            ['label' => 'Registered donors', 'value' => $stats['donors'], 'hint' => 'In the donor registry', 'tone' => 'bg-sky-50 text-sky-600', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>'],
            ['label' => 'Eligible to donate', 'value' => $stats['eligible'], 'hint' => 'Passed the 90-day deferral', 'tone' => 'bg-emerald-50 text-emerald-600', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>'],
            ['label' => 'Units collected', 'value' => $stats['units'], 'hint' => 'Across all blood groups', 'tone' => 'bg-rose-50 text-rose-600', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>'],
            ['label' => 'Open requests', 'value' => $stats['openRequests'], 'hint' => 'Awaiting fulfilment', 'tone' => 'bg-amber-50 text-amber-600', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>'],
        ];
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as $card)
            <div class="card p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[13px] font-medium text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($card['value']) }}</p>
                    </div>
                    <span class="grid size-10 place-items-center rounded-xl {{ $card['tone'] }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-5">{!! $card['icon'] !!}</svg>
                    </span>
                </div>
                <p class="mt-3 text-xs text-slate-400">{{ $card['hint'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="card xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Blood stock</h2>
                    <p class="text-xs text-slate-500">Collected units minus units dispensed for fulfilled requests</p>
                </div>
                <span class="text-xs font-medium text-slate-400">Units</span>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach ($stock as $group => $item)
                    @php
                        $available = $item['available'];
                        $percent = min(100, ($available / 25) * 100);
                        $tone = $available < 5 ? 'bg-rose-500' : ($available < 12 ? 'bg-amber-500' : 'bg-emerald-500');
                        $status = $available < 5 ? ['Low stock', 'text-rose-600 bg-rose-50'] : ($available < 12 ? ['Moderate', 'text-amber-600 bg-amber-50'] : ['Healthy', 'text-emerald-600 bg-emerald-50']);
                    @endphp
                    <div class="flex items-center gap-4 px-5 py-3">
                        <x-blood-badge :group="$group" class="w-12 justify-center" />
                        <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full {{ $tone }} transition-all duration-700" style="width: {{ $percent }}%"></div>
                        </div>
                        <span class="w-8 text-right text-sm font-bold text-slate-900">{{ $available }}</span>
                        <span class="hidden w-24 text-right text-xs font-semibold sm:inline {{ $status[1] }} rounded-md px-2 py-1">{{ $status[0] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="card">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-bold text-slate-900">Donors by blood group</h2>
                <p class="text-xs text-slate-500">Registry distribution</p>
            </div>

            <div class="space-y-3.5 p-5">
                @foreach (\App\Support\BloodGroup::ALL as $group)
                    @php $count = (int) ($distribution[$group] ?? 0); @endphp
                    <div>
                        <div class="mb-1.5 flex items-center justify-between text-xs">
                            <span class="font-semibold text-slate-600">{{ $group }}</span>
                            <span class="font-medium text-slate-400">{{ $count }} donor{{ $count === 1 ? '' : 's' }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full {{ \App\Support\BloodGroup::bar($group) }} transition-all duration-700"
                                 style="width: {{ ($count / $maxDistribution) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="card xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Recent donations</h2>
                    <p class="text-xs text-slate-500">Latest units added to the bank</p>
                </div>
                <a href="{{ route('donations.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-700">View all</a>
            </div>

            @if ($recentDonations->isEmpty())
                <div class="px-5 py-10 text-center text-sm text-slate-500">No donations recorded yet.</div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($recentDonations as $donation)
                        <li class="flex items-center gap-3 px-5 py-3.5">
                            <x-avatar :name="$donation->donor->name" :group="$donation->donor->blood_group" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">
                                    <a href="{{ route('donors.show', $donation->donor) }}" class="hover:text-rose-600">{{ $donation->donor->name }}</a>
                                </p>
                                <p class="truncate text-xs text-slate-400">{{ $donation->camp ?: 'Blood bank drive' }} · {{ $donation->donation_date->format('d M Y') }}</p>
                            </div>
                            <x-blood-badge :group="$donation->donor->blood_group" />
                            <span class="w-14 text-right text-sm font-bold text-slate-900">{{ $donation->units }}u</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="card">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Priority requests</h2>
                    <p class="text-xs text-slate-500">Open and urgent first</p>
                </div>
                <a href="{{ route('requests.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-700">View all</a>
            </div>

            @if ($urgentRequests->isEmpty())
                <div class="px-5 py-10 text-center text-sm text-slate-500">No open requests right now.</div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($urgentRequests as $request)
                        <li>
                            <a href="{{ route('requests.show', $request) }}" class="block px-5 py-3.5 transition hover:bg-slate-50">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $request->patient_name }}</p>
                                    <x-blood-badge :group="$request->blood_group" />
                                </div>
                                <div class="mt-1.5 flex items-center justify-between gap-2 text-xs text-slate-500">
                                    <span class="truncate">{{ $request->hospital }}</span>
                                    <span class="shrink-0 font-medium {{ $request->urgency === 'critical' ? 'text-rose-600' : ($request->urgency === 'urgent' ? 'text-amber-600' : 'text-slate-400') }}">
                                        {{ ucfirst($request->urgency) }} · {{ $request->units_needed - $request->units_fulfilled }}u needed
                                    </span>
                                </div>
                                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full {{ $request->progress >= 100 ? 'bg-emerald-500' : 'bg-rose-500' }}" style="width: {{ max(4, $request->progress) }}%"></div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
