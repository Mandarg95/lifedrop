@extends('layouts.app')

@section('title', 'Record donation')
@section('subtitle', 'Log a new unit collected from a donor')

@section('content')
    <div class="mb-6">
        <a href="{{ route('donations.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-800">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to donations
        </a>
    </div>

    @if ($donors->isEmpty())
        <x-empty-state title="No donors registered yet" message="A donor must exist before a donation can be recorded.">
            <x-slot:action>
                <a href="{{ route('donors.create') }}" class="btn-primary">Add donor</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <form method="POST" action="{{ route('donations.store') }}" novalidate id="donation-form">
            @csrf

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <section class="card p-5 sm:p-6">
                        <h2 class="mb-4 text-sm font-bold text-slate-900">Donation details</h2>

                        <div>
                            <label for="donor_id" class="label">Donor <span class="text-rose-500">*</span></label>
                            <select name="donor_id" id="donor_id" required class="input @error('donor_id') input-error @enderror">
                                <option value="">Select a donor…</option>
                                @foreach ($donors as $donor)
                                    <option value="{{ $donor->id }}"
                                            @selected((int) old('donor_id', $preselected) === $donor->id)
                                            data-name="{{ $donor->name }}"
                                            data-group="{{ $donor->blood_group }}"
                                            data-available="{{ $donor->is_available ? '1' : '0' }}"
                                            data-last="{{ $donor->lastDonationDate() }}"
                                            data-next="{{ $donor->nextEligibleDate() }}">
                                        {{ $donor->name }} — {{ $donor->blood_group }}{{ $donor->city ? ' · '.$donor->city : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('donor_id')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div id="donor-warning" class="mt-4 hidden rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <div class="flex gap-3">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mt-0.5 size-5 shrink-0 text-amber-500"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                                <div class="text-sm text-amber-800">
                                    <p class="font-semibold" id="donor-warning-title">Deferral period</p>
                                    <p class="mt-0.5" id="donor-warning-body"></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="donation_date" class="label">Donation date <span class="text-rose-500">*</span></label>
                                <input type="date" name="donation_date" id="donation_date" required max="{{ now()->toDateString() }}"
                                       value="{{ old('donation_date', now()->toDateString()) }}"
                                       class="input @error('donation_date') input-error @enderror">
                                @error('donation_date')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="label">Units collected <span class="text-rose-500">*</span></label>
                                <div class="flex gap-3">
                                    @foreach ([1, 2] as $unit)
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="units" value="{{ $unit }}" class="peer sr-only"
                                                   @checked((int) old('units', 1) === $unit)>
                                            <span class="grid h-[42px] place-items-center rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-600 transition
                                                         peer-checked:border-rose-600 peer-checked:bg-rose-600 peer-checked:text-white peer-checked:shadow-sm peer-checked:shadow-rose-600/30
                                                         hover:border-slate-300">{{ $unit }} unit{{ $unit > 1 ? 's' : '' }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('units')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="camp" class="label">Camp / location <span class="font-normal text-slate-400">(optional)</span></label>
                                <input type="text" name="camp" id="camp" value="{{ old('camp') }}"
                                       placeholder="e.g. City Hospital blood drive" class="input @error('camp') input-error @enderror">
                                @error('camp')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="notes" class="label">Notes <span class="font-normal text-slate-400">(optional)</span></label>
                                <textarea name="notes" id="notes" rows="3" placeholder="Reaction of donor, phlebotomist, batch no…"
                                          class="input resize-y @error('notes') input-error @enderror">{{ old('notes') }}</textarea>
                                @error('notes')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </section>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" class="btn-primary sm:w-56">Record donation</button>
                        <a href="{{ route('donations.index') }}" class="btn-secondary sm:w-32">Cancel</a>
                    </div>
                </div>

                <aside>
                    <section class="card p-5">
                        <h2 class="mb-3 text-sm font-bold text-slate-900">Donation checklist</h2>
                        <ul class="space-y-3 text-sm text-slate-600">
                            <li class="flex gap-2.5"><span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-rose-400"></span>Donor has rested and eaten before donation.</li>
                            <li class="flex gap-2.5"><span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-rose-400"></span>Hemoglobin and weight meet the eligibility threshold.</li>
                            <li class="flex gap-2.5"><span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-rose-400"></span>At least {{ \App\Models\Donor::DEFERRAL_DAYS }} days since the previous whole-blood donation.</li>
                            <li class="flex gap-2.5"><span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-rose-400"></span>Post-donation observation completed (10–15 minutes).</li>
                        </ul>
                    </section>
                </aside>
            </div>
        </form>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            const select = document.getElementById('donor_id');
            const warning = document.getElementById('donor-warning');
            const title = document.getElementById('donor-warning-title');
            const body = document.getElementById('donor-warning-body');
            if (!select || !warning) return;

            const update = () => {
                const option = select.options[select.selectedIndex];
                if (!option || !option.value) {
                    warning.classList.add('hidden');
                    return;
                }

                const available = option.dataset.available === '1';
                const next = option.dataset.next;
                const last = option.dataset.last;
                const name = option.dataset.name;

                if (!available) {
                    title.textContent = 'Donor marked unavailable';
                    body.textContent = name + ' is currently deferred in the registry. Re-enable the donor before recording a donation.';
                    warning.classList.remove('hidden');
                    return;
                }

                if (next && new Date(next) > new Date(new Date().toDateString())) {
                    title.textContent = 'Still inside the deferral period';
                    body.textContent = name + ' last donated on ' + last + ' and becomes eligible again on ' + next + '. Recording now will be blocked.';
                    warning.classList.remove('hidden');
                    return;
                }

                warning.classList.add('hidden');
            };

            select.addEventListener('change', update);
            update();
        })();
    </script>
@endpush
