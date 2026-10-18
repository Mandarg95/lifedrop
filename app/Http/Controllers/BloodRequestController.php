<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Support\BloodGroup;
use Illuminate\Http\Request;

class BloodRequestController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q' => $request->query('q'),
            'status' => $request->query('status'),
            'blood_group' => $request->query('blood_group'),
            'urgency' => $request->query('urgency'),
        ];

        $requests = BloodRequest::query()
            ->search($filters['q'])
            ->filter($filters['status'], $filters['blood_group'], $filters['urgency'])
            ->orderByRaw("CASE urgency WHEN 'critical' THEN 0 WHEN 'urgent' THEN 1 ELSE 2 END")
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('requests.index', [
            'requests' => $requests,
            'filters' => $filters,
            'groups' => BloodGroup::ALL,
            'counts' => [
                'pending' => BloodRequest::where('status', 'pending')->count(),
                'fulfilled' => BloodRequest::where('status', 'fulfilled')->count(),
                'cancelled' => BloodRequest::where('status', 'cancelled')->count(),
            ],
        ]);
    }

    public function create()
    {
        return view('requests.create', ['groups' => BloodGroup::ALL]);
    }

    public function store(Request $request)
    {
        $bloodRequest = BloodRequest::create($this->validated($request));

        return redirect()->route('requests.show', $bloodRequest)
            ->with('success', 'Blood request created.');
    }

    public function show(BloodRequest $bloodRequest)
    {
        return view('requests.show', ['bloodRequest' => $bloodRequest]);
    }

    public function edit(BloodRequest $bloodRequest)
    {
        return view('requests.edit', ['bloodRequest' => $bloodRequest, 'groups' => BloodGroup::ALL]);
    }

    public function update(Request $request, BloodRequest $bloodRequest)
    {
        $bloodRequest->update($this->validated($request, $bloodRequest));

        return redirect()->route('requests.show', $bloodRequest)
            ->with('success', 'Blood request updated.');
    }

    public function fulfill(BloodRequest $bloodRequest)
    {
        $bloodRequest->update([
            'status' => 'fulfilled',
            'units_fulfilled' => $bloodRequest->units_needed,
        ]);

        return redirect()->route('requests.show', $bloodRequest)->with('success', 'Request marked as fulfilled.');
    }

    public function cancel(BloodRequest $bloodRequest)
    {
        $bloodRequest->update(['status' => 'cancelled']);

        return redirect()->route('requests.show', $bloodRequest)->with('success', 'Request cancelled.');
    }

    public function destroy(BloodRequest $bloodRequest)
    {
        $bloodRequest->delete();

        return redirect()->route('requests.index')->with('success', 'Request deleted.');
    }

    private function validated(Request $request, ?BloodRequest $bloodRequest = null): array
    {
        $data = $request->validate([
            'patient_name' => ['required', 'string', 'max:120'],
            'blood_group' => ['required', 'string', 'in:'.implode(',', BloodGroup::ALL)],
            'units_needed' => ['required', 'integer', 'min:1', 'max:10'],
            'units_fulfilled' => ['nullable', 'integer', 'min:0', 'max:10', 'lte:units_needed'],
            'hospital' => ['required', 'string', 'max:160'],
            'contact_name' => ['required', 'string', 'max:120'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'needed_by' => ['nullable', 'date', 'after_or_equal:today'],
            'urgency' => ['required', 'in:routine,urgent,critical'],
            'status' => ['required', 'in:pending,fulfilled,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['units_fulfilled'] = (int) ($data['units_fulfilled'] ?? ($bloodRequest?->units_fulfilled ?? 0));

        if ($data['status'] === 'fulfilled') {
            $data['units_fulfilled'] = $data['units_needed'];
        }

        return $data;
    }
}
