<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use App\Support\BloodGroup;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q' => $request->query('q'),
            'blood_group' => $request->query('blood_group'),
            'period' => $request->query('period'),
        ];

        $donations = Donation::query()
            ->with('donor')
            ->search($filters['q'])
            ->filter($filters['blood_group'], $filters['period'])
            ->orderByDesc('donation_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('donations.index', [
            'donations' => $donations,
            'filters' => $filters,
            'groups' => BloodGroup::ALL,
            'totalUnits' => (int) Donation::sum('units'),
        ]);
    }

    public function create(Request $request)
    {
        $preselected = $request->integer('donor');

        return view('donations.create', [
            'donors' => Donor::orderBy('name')->get(),
            'preselected' => $preselected,
            'groups' => BloodGroup::ALL,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'donor_id' => ['required', 'exists:donors,id'],
            'donation_date' => ['required', 'date', 'before_or_equal:today'],
            'units' => ['required', 'integer', 'min:1', 'max:2'],
            'camp' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $donor = Donor::findOrFail($data['donor_id']);

        if ($donor->latestDonation()
            && $donor->latestDonation()->donation_date->copy()->addDays(Donor::DEFERRAL_DAYS)->isFuture()
            && $donor->is_available) {
            return back()
                ->withInput()
                ->withErrors(['donation_date' => "{$donor->name} last donated on {$donor->lastDonationDate()} and is eligible again on {$donor->nextEligibleDate()}."]);
        }

        $donation = $donor->donations()->create($data);

        return redirect()->route('donations.index')
            ->with('success', "Donation recorded — {$donation->units} unit".($donation->units > 1 ? 's' : '')." from {$donor->name}.");
    }

    public function destroy(Donation $donation)
    {
        $name = $donation->donor->name;
        $donation->delete();

        return redirect()->route('donations.index')->with('success', "Donation record for {$name} deleted.");
    }
}
