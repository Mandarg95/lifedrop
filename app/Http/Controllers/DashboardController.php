<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Donor;
use App\Support\BloodGroup;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $groups = BloodGroup::ALL;

        $collected = Donation::query()
            ->join('donors', 'donors.id', '=', 'donations.donor_id')
            ->selectRaw('donors.blood_group as bg, SUM(donations.units) as units')
            ->groupBy('donors.blood_group')
            ->pluck('units', 'bg');

        $dispensed = BloodRequest::where('status', 'fulfilled')
            ->selectRaw('blood_group as bg, SUM(units_fulfilled) as units')
            ->groupBy('blood_group')
            ->pluck('units', 'bg');

        $stock = collect($groups)->mapWithKeys(function (string $group) use ($collected, $dispensed) {
            $in = (int) ($collected[$group] ?? 0);
            $out = (int) ($dispensed[$group] ?? 0);

            return [$group => [
                'collected' => $in,
                'dispensed' => $out,
                'available' => max(0, $in - $out),
            ]];
        });

        $donorDistribution = Donor::query()
            ->selectRaw('blood_group as bg, COUNT(*) as total')
            ->groupBy('blood_group')
            ->pluck('total', 'bg');

        return view('dashboard', [
            'stats' => [
                'donors' => Donor::count(),
                'eligible' => Donor::where('is_available', true)
                    ->whereNotExists(function ($q) {
                        $q->select(DB::raw(1))
                            ->from('donations')
                            ->whereColumn('donations.donor_id', 'donors.id')
                            ->where('donations.donation_date', '>=', now()->subDays(Donor::DEFERRAL_DAYS)->toDateString());
                    })
                    ->count(),
                'units' => (int) Donation::sum('units'),
                'openRequests' => BloodRequest::where('status', 'pending')->count(),
            ],
            'stock' => $stock,
            'distribution' => $donorDistribution,
            'maxDistribution' => max(1, (int) $donorDistribution->max()),
            'recentDonations' => Donation::with('donor')->latest('donation_date')->latest('id')->take(6)->get(),
            'urgentRequests' => BloodRequest::where('status', 'pending')
                ->orderByRaw("CASE urgency WHEN 'critical' THEN 0 WHEN 'urgent' THEN 1 ELSE 2 END")
                ->orderBy('needed_by')
                ->take(5)
                ->get(),
        ]);
    }
}
