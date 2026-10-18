@php $donor = $donor ?? null; @endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <section class="card p-5 sm:p-6">
            <h2 class="mb-4 text-sm font-bold text-slate-900">Personal details</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="label">Full name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $donor?->name) }}" required
                           placeholder="e.g. Aarav Sharma" class="input @error('name') input-error @enderror">
                    @error('name')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="label">Phone <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $donor?->phone) }}" required
                           placeholder="98765 43210" class="input @error('phone') input-error @enderror">
                    @error('phone')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="label">Email <span class="font-normal text-slate-400">(optional)</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $donor?->email) }}"
                           placeholder="donor@example.com" class="input @error('email') input-error @enderror">
                    @error('email')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="date_of_birth" class="label">Date of birth <span class="font-normal text-slate-400">(optional)</span></label>
                    <input type="date" name="date_of_birth" id="date_of_birth"
                           value="{{ old('date_of_birth', $donor?->date_of_birth?->format('Y-m-d')) }}"
                           max="{{ now()->subYear(18)->format('Y-m-d') }}"
                           class="input @error('date_of_birth') input-error @enderror">
                    @error('date_of_birth')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="gender" class="label">Gender <span class="font-normal text-slate-400">(optional)</span></label>
                    <select name="gender" id="gender" class="input @error('gender') input-error @enderror">
                        <option value="">Prefer not to say</option>
                        @foreach (['male', 'female', 'other'] as $option)
                            <option value="{{ $option }}" @selected(old('gender', $donor?->gender) === $option)>{{ ucfirst($option) }}</option>
                        @endforeach
                    </select>
                    @error('gender')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="card p-5 sm:p-6">
            <h2 class="mb-4 text-sm font-bold text-slate-900">Address & health</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="city" class="label">City <span class="font-normal text-slate-400">(optional)</span></label>
                    <input type="text" name="city" id="city" value="{{ old('city', $donor?->city) }}"
                           placeholder="e.g. Pune" class="input @error('city') input-error @enderror">
                    @error('city')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="weight_kg" class="label">Weight (kg) <span class="font-normal text-slate-400">(optional)</span></label>
                    <input type="number" name="weight_kg" id="weight_kg" step="0.1" min="45" max="200"
                           value="{{ old('weight_kg', $donor?->weight_kg) }}" placeholder="62"
                           class="input @error('weight_kg') input-error @enderror">
                    @error('weight_kg')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="address" class="label">Address <span class="font-normal text-slate-400">(optional)</span></label>
                    <input type="text" name="address" id="address" value="{{ old('address', $donor?->address) }}"
                           placeholder="Street, area, landmark" class="input @error('address') input-error @enderror">
                    @error('address')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="medical_notes" class="label">Medical notes <span class="font-normal text-slate-400">(optional)</span></label>
                    <textarea name="medical_notes" id="medical_notes" rows="3"
                              placeholder="Allergies, medications, previous deferrals…"
                              class="input resize-y @error('medical_notes') input-error @enderror">{{ old('medical_notes', $donor?->medical_notes) }}</textarea>
                    @error('medical_notes')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>
    </div>

    <aside class="space-y-6">
        <section class="card p-5 sm:p-6">
            <h2 class="mb-4 text-sm font-bold text-slate-900">Donation profile</h2>

            <div>
                <label for="blood_group" class="label">Blood group <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-4 gap-2">
                    @foreach ($groups as $group)
                        <label class="cursor-pointer">
                            <input type="radio" name="blood_group" value="{{ $group }}" class="peer sr-only"
                                   @checked(old('blood_group', $donor?->blood_group) === $group)>
                            <span class="grid h-11 place-items-center rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-600 transition
                                         peer-checked:border-rose-600 peer-checked:bg-rose-600 peer-checked:text-white peer-checked:shadow-sm peer-checked:shadow-rose-600/30
                                         hover:border-slate-300">{{ $group }}</span>
                        </label>
                    @endforeach
                </div>
                @error('blood_group')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="mt-5">
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3.5 transition hover:bg-slate-50">
                    <input type="checkbox" name="is_available" value="1" class="mt-0.5 size-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500/30"
                           @checked(old('is_available', $donor?->is_available ?? true))>
                    <span>
                        <span class="block text-sm font-semibold text-slate-800">Available to donate</span>
                        <span class="mt-0.5 block text-xs text-slate-500">Uncheck to temporarily defer this donor (travel, illness, medication).</span>
                    </span>
                </label>
                @error('is_available')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
            </div>
        </section>

        <div class="flex flex-col gap-3">
            <button type="submit" class="btn-primary w-full">{{ $submitLabel ?? 'Save donor' }}</button>
            <a href="{{ $cancelUrl }}" class="btn-secondary w-full">Cancel</a>
        </div>
    </aside>
</div>
