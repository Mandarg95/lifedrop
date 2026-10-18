@php $bloodRequest = $bloodRequest ?? null; @endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <section class="card p-5 sm:p-6">
            <h2 class="mb-4 text-sm font-bold text-slate-900">Patient & requirement</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="patient_name" class="label">Patient name <span class="text-rose-500">*</span></label>
                    <input type="text" name="patient_name" id="patient_name" required
                           value="{{ old('patient_name', $bloodRequest?->patient_name) }}" placeholder="e.g. Meera Iyer"
                           class="input @error('patient_name') input-error @enderror">
                    @error('patient_name')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="hospital" class="label">Hospital / clinic <span class="text-rose-500">*</span></label>
                    <input type="text" name="hospital" id="hospital" required
                           value="{{ old('hospital', $bloodRequest?->hospital) }}" placeholder="e.g. Ruby Hall Clinic"
                           class="input @error('hospital') input-error @enderror">
                    @error('hospital')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="needed_by" class="label">Needed by <span class="font-normal text-slate-400">(optional)</span></label>
                    <input type="date" name="needed_by" id="needed_by" min="{{ now()->toDateString() }}"
                           value="{{ old('needed_by', $bloodRequest?->needed_by?->format('Y-m-d')) }}"
                           class="input @error('needed_by') input-error @enderror">
                    @error('needed_by')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="label">Blood group <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-4 gap-2 sm:grid-cols-8">
                        @foreach ($groups as $group)
                            <label class="cursor-pointer">
                                <input type="radio" name="blood_group" value="{{ $group }}" class="peer sr-only"
                                       @checked(old('blood_group', $bloodRequest?->blood_group) === $group)>
                                <span class="grid h-11 place-items-center rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-600 transition
                                             peer-checked:border-rose-600 peer-checked:bg-rose-600 peer-checked:text-white peer-checked:shadow-sm peer-checked:shadow-rose-600/30
                                             hover:border-slate-300">{{ $group }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('blood_group')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="units_needed" class="label">Units needed <span class="text-rose-500">*</span></label>
                    <input type="number" name="units_needed" id="units_needed" min="1" max="10" required
                           value="{{ old('units_needed', $bloodRequest?->units_needed ?? 1) }}"
                           class="input @error('units_needed') input-error @enderror">
                    @error('units_needed')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="units_fulfilled" class="label">Units fulfilled</label>
                    <input type="number" name="units_fulfilled" id="units_fulfilled" min="0" max="10"
                           value="{{ old('units_fulfilled', $bloodRequest?->units_fulfilled ?? 0) }}"
                           class="input @error('units_fulfilled') input-error @enderror">
                    @error('units_fulfilled')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="card p-5 sm:p-6">
            <h2 class="mb-4 text-sm font-bold text-slate-900">Contact person</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="contact_name" class="label">Contact name <span class="text-rose-500">*</span></label>
                    <input type="text" name="contact_name" id="contact_name" required
                           value="{{ old('contact_name', $bloodRequest?->contact_name) }}" placeholder="Relative or attending staff"
                           class="input @error('contact_name') input-error @enderror">
                    @error('contact_name')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="contact_phone" class="label">Contact phone <span class="text-rose-500">*</span></label>
                    <input type="text" name="contact_phone" id="contact_phone" required
                           value="{{ old('contact_phone', $bloodRequest?->contact_phone) }}" placeholder="98765 43210"
                           class="input @error('contact_phone') input-error @enderror">
                    @error('contact_phone')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="label">Notes <span class="font-normal text-slate-400">(optional)</span></label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Diagnosis, ward, surgery date…"
                              class="input resize-y @error('notes') input-error @enderror">{{ old('notes', $bloodRequest?->notes) }}</textarea>
                    @error('notes')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <div class="flex flex-col gap-3 sm:flex-row">
            <button type="submit" class="btn-primary sm:w-56">{{ $submitLabel ?? 'Save request' }}</button>
            <a href="{{ $cancelUrl }}" class="btn-secondary sm:w-32">Cancel</a>
        </div>
    </div>

    <aside class="space-y-6">
        <section class="card p-5 sm:p-6">
            <h2 class="mb-4 text-sm font-bold text-slate-900">Triage</h2>

            <div>
                <label for="urgency" class="label">Urgency <span class="text-rose-500">*</span></label>
                <select name="urgency" id="urgency" class="input @error('urgency') input-error @enderror">
                    @foreach (['routine' => 'Routine — standard waiting window', 'urgent' => 'Urgent — needed within 24 hours', 'critical' => 'Critical — immediate transfusion'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('urgency', $bloodRequest?->urgency ?? 'routine') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('urgency')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="mt-5">
                <label for="status" class="label">Status <span class="text-rose-500">*</span></label>
                <select name="status" id="status" class="input @error('status') input-error @enderror">
                    @foreach (['pending' => 'Pending — still collecting', 'fulfilled' => 'Fulfilled — all units arranged', 'cancelled' => 'Cancelled'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $bloodRequest?->status ?? 'pending') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-slate-400">Marking as fulfilled sets units fulfilled to units needed.</p>
                @error('status')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
            </div>
        </section>

        <div class="rounded-2xl border border-sky-100 bg-sky-50 p-5 text-sm text-sky-900">
            <p class="font-semibold">Keep contact details current</p>
            <p class="mt-1 text-xs leading-relaxed text-sky-800/80">Requests are listed by urgency first, then by deadline. Donors matching this blood group can be contacted from the donor registry.</p>
        </div>
    </aside>
</div>
