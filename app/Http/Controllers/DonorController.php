<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Support\BloodGroup;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q' => $request->query('q'),
            'blood_group' => $request->query('blood_group'),
            'availability' => $request->query('availability'),
        ];

        $donors = Donor::query()
            ->search($filters['q'])
            ->filter($filters['blood_group'], $filters['availability'])
            ->with('donations:id,donor_id,donation_date,units')
            ->withCount('donations')
            ->withMax('donations as last_donation_date', 'donation_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('donors.index', [
            'donors' => $donors,
            'filters' => $filters,
            'groups' => BloodGroup::ALL,
        ]);
    }

    public function create()
    {
        return view('donors.create', ['groups' => BloodGroup::ALL]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());

        $data['is_available'] = $request->boolean('is_available');

        Donor::create($data);

        return redirect()->route('donors.index')->with('success', 'Donor added successfully.');
    }

    public function show(Donor $donor)
    {
        $donor->load(['donations' => fn ($q) => $q->orderByDesc('donation_date')]);

        return view('donors.show', [
            'donor' => $donor,
            'unitsDonated' => (int) $donor->donations->sum('units'),
        ]);
    }

    public function edit(Donor $donor)
    {
        return view('donors.edit', ['donor' => $donor, 'groups' => BloodGroup::ALL]);
    }

    public function update(Request $request, Donor $donor)
    {
        $data = $request->validate($this->rules($donor), $this->messages());

        $data['is_available'] = $request->boolean('is_available');

        $donor->update($data);

        return redirect()->route('donors.show', $donor)->with('success', 'Donor updated successfully.');
    }

    public function destroy(Donor $donor)
    {
        $donor->delete();

        return redirect()->route('donors.index')->with('success', 'Donor deleted.');
    }

    private function rules(?Donor $donor = null): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:160', 'unique:donors,email'.($donor ? ','.$donor->id : '')],
            'phone' => ['required', 'string', 'max:20'],
            'blood_group' => ['required', 'string', 'in:'.implode(',', BloodGroup::ALL)],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'city' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:190'],
            'weight_kg' => ['nullable', 'numeric', 'min:45', 'max:200'],
            'medical_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function messages(): array
    {
        return [
            'blood_group.in' => 'Please select a valid blood group.',
            'weight_kg.min' => 'Donors must weigh at least 45 kg to donate.',
        ];
    }
}
