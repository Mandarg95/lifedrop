<?php

namespace Tests\Feature;

use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Donor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BloodDonorTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_donors_and_requests_pages_render(): void
    {
        $this->get('/')->assertOk();
        $this->get('/donors')->assertOk();
        $this->get('/donations')->assertOk();
        $this->get('/requests')->assertOk();
    }

    public function test_a_donor_can_be_created(): void
    {
        $response = $this->post('/donors', [
            '_token' => 'test',
            'name' => 'Sara Khan',
            'phone' => '9123456789',
            'blood_group' => 'O+',
            'city' => 'Pune',
            'is_available' => '1',
        ]);

        $response->assertRedirect('/donors');
        $this->assertDatabaseHas('donors', ['name' => 'Sara Khan', 'blood_group' => 'O+']);
    }

    public function test_donor_validation_rejects_bad_input(): void
    {
        $this->from('/donors/create')
            ->post('/donors', [
                '_token' => 'test',
                'name' => '',
                'phone' => '',
                'blood_group' => 'ZZ',
                'weight_kg' => 20,
            ])
            ->assertRedirect('/donors/create')
            ->assertSessionHasErrors(['name', 'phone', 'blood_group', 'weight_kg']);
    }

    public function test_donation_is_blocked_during_the_deferral_window(): void
    {
        $donor = Donor::create([
            'name' => 'Recent Donor',
            'phone' => '9000000001',
            'blood_group' => 'A+',
            'is_available' => true,
        ]);

        $donor->donations()->create([
            'donation_date' => now()->subDays(20),
            'units' => 1,
        ]);

        $this->post('/donations', [
            '_token' => 'test',
            'donor_id' => $donor->id,
            'donation_date' => now()->toDateString(),
            'units' => 1,
        ])->assertSessionHasErrors('donation_date');

        $this->assertSame(1, Donation::count());
    }

    public function test_a_donation_can_be_recorded_for_an_eligible_donor(): void
    {
        $donor = Donor::create([
            'name' => 'Eligible Donor',
            'phone' => '9000000002',
            'blood_group' => 'B-',
            'is_available' => true,
        ]);

        $this->post('/donations', [
            '_token' => 'test',
            'donor_id' => $donor->id,
            'donation_date' => now()->toDateString(),
            'units' => 1,
            'camp' => 'Test camp',
        ])->assertRedirect('/donations');

        $this->assertDatabaseHas('donations', ['donor_id' => $donor->id, 'camp' => 'Test camp']);
    }

    public function test_a_blood_request_can_be_created_and_fulfilled(): void
    {
        $response = $this->post('/requests', [
            '_token' => 'test',
            'patient_name' => 'Ravi Kumar',
            'blood_group' => 'AB+',
            'units_needed' => 2,
            'hospital' => 'City Hospital',
            'contact_name' => 'Suresh Kumar',
            'contact_phone' => '9111111111',
            'needed_by' => now()->addDay()->toDateString(),
            'urgency' => 'critical',
            'status' => 'pending',
        ]);

        $request = BloodRequest::where('patient_name', 'Ravi Kumar')->firstOrFail();
        $response->assertRedirect('/requests/'.$request->id);

        $this->patch('/requests/'.$request->id.'/fulfill', ['_token' => 'test'])
            ->assertRedirect('/requests/'.$request->id);

        $this->assertDatabaseHas('blood_requests', [
            'id' => $request->id,
            'status' => 'fulfilled',
            'units_fulfilled' => 2,
        ]);
    }

    public function test_donor_index_filters_by_blood_group_and_availability(): void
    {
        Donor::create(['name' => 'Available One', 'phone' => '9000000003', 'blood_group' => 'O-', 'is_available' => true]);
        Donor::create(['name' => 'Blocked One', 'phone' => '9000000004', 'blood_group' => 'O-', 'is_available' => false]);

        $this->get('/donors?blood_group=O-&availability=eligible')
            ->assertOk()
            ->assertSee('Available One')
            ->assertDontSee('Blocked One');
    }
}
