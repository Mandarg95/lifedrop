<?php

namespace Database\Seeders;

use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private const GROUPS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    private const FIRST_NAMES = [
        'Aarav', 'Vivaan', 'Aditya', 'Arjun', 'Reyansh', 'Krishna', 'Ishaan', 'Shaurya', 'Atharv', 'Advik',
        'Ananya', 'Diya', 'Aadhya', 'Myra', 'Anika', 'Navya', 'Ira', 'Saanvi', 'Pihu', 'Riya',
        'Rohan', 'Kabir', 'Zoya', 'Meera', 'Dev', 'Neha', 'Rahul', 'Priya', 'Siddharth', 'Tanvi',
    ];

    private const LAST_NAMES = [
        'Sharma', 'Verma', 'Patel', 'Reddy', 'Nair', 'Iyer', 'Kulkarni', 'Desai', 'Joshi', 'Mehta',
        'Kapoor', 'Malhotra', 'Chopra', 'Banerjee', 'Mukherjee', 'Rao', 'Singh', 'Gupta', 'Das', 'Bose',
    ];

    private const CITIES = ['Pune', 'Mumbai', 'Bengaluru', 'Delhi', 'Hyderabad', 'Chennai', 'Kolkata', 'Ahmedabad', 'Jaipur', 'Lucknow'];

    private const CAMPS = [
        'City Hospital blood drive',
        'Metro General Camp',
        'Rotary Club drive',
        'University health camp',
        'Red Cross mobile unit',
        'Corporate wellness drive',
    ];

    public function run(): void
    {
        $donors = [];

        foreach (range(1, 48) as $i) {
            $group = self::GROUPS[array_rand(self::GROUPS)];
            $name = self::FIRST_NAMES[array_rand(self::FIRST_NAMES)].' '.self::LAST_NAMES[array_rand(self::LAST_NAMES)];

            $donors[] = Donor::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)).$i.'@example.com',
                'phone' => '9'.random_int(100000000, 999999999),
                'blood_group' => $group,
                'date_of_birth' => now()->subYears(random_int(21, 54))->subDays(random_int(0, 364))->toDateString(),
                'gender' => ['male', 'female', 'other'][array_rand(['male', 'female', 'other'])],
                'city' => self::CITIES[array_rand(self::CITIES)],
                'address' => random_int(1, 120).' '.['MG Road', 'Station Road', 'Lake View', 'Green Park', 'Hill Side'][array_rand(['MG Road', 'Station Road', 'Lake View', 'Green Park', 'Hill Side'])],
                'weight_kg' => round(random_int(50, 92) + random_int(0, 9) / 10, 1),
                'is_available' => random_int(1, 10) > 1,
                'medical_notes' => random_int(1, 6) === 1 ? 'Mild reaction on first donation, observed for 20 minutes.' : null,
            ]);
        }

        foreach ($donors as $donor) {
            $count = match (random_int(0, 10)) {
                0, 1, 2 => 0,
                3, 4, 5, 6, 7 => 1,
                8, 9 => 2,
                default => 3,
            };

            $date = now()->subDays(random_int(120, 900));

            foreach (range(1, $count) as $n) {
                Donation::create([
                    'donor_id' => $donor->id,
                    'donation_date' => $date->copy()->subMonths(($count - $n) * random_int(4, 7))->toDateString(),
                    'units' => random_int(1, 5) === 1 ? 2 : 1,
                    'camp' => self::CAMPS[array_rand(self::CAMPS)],
                    'notes' => null,
                ]);
            }
        }

        $patients = ['Meera Iyer', 'Rohan Desai', 'Zoya Khan', 'Dev Patel', 'Anika Sharma', 'Kabir Nair', 'Ira Joshi', 'Advik Rao'];

        foreach ($patients as $index => $patient) {
            $status = ['pending', 'pending', 'pending', 'fulfilled', 'fulfilled', 'cancelled'][array_rand([0, 1, 2, 3, 4, 5])];
            $needed = random_int(1, 6);

            BloodRequest::create([
                'patient_name' => $patient,
                'blood_group' => self::GROUPS[array_rand(self::GROUPS)],
                'units_needed' => $needed,
                'units_fulfilled' => $status === 'fulfilled' ? $needed : ($status === 'pending' ? random_int(0, max(0, $needed - 1)) : 0),
                'hospital' => ['Ruby Hall Clinic', 'Sassoon Hospital', 'Apollo Speciality', 'Fortis Hospital'][array_rand(['Ruby Hall Clinic', 'Sassoon Hospital', 'Apollo Speciality', 'Fortis Hospital'])],
                'contact_name' => self::FIRST_NAMES[array_rand(self::FIRST_NAMES)].' '.self::LAST_NAMES[array_rand(self::LAST_NAMES)],
                'contact_phone' => '9'.random_int(100000000, 999999999),
                'needed_by' => now()->addDays($index - 2)->toDateString(),
                'urgency' => ['routine', 'urgent', 'critical'][array_rand(['routine', 'urgent', 'critical'])],
                'status' => $status,
                'notes' => $index % 3 === 0 ? 'Scheduled surgery, cross-match samples already collected.' : null,
            ]);
        }

        // A freshly donated, eligible demo donor.
        Donor::create([
            'name' => 'Aarav Sharma',
            'email' => 'aarav.sharma@example.com',
            'phone' => '9876543210',
            'blood_group' => 'O-',
            'date_of_birth' => '1996-04-12',
            'gender' => 'male',
            'city' => 'Pune',
            'address' => '42 Baner Road',
            'weight_kg' => 72.4,
            'is_available' => true,
            'medical_notes' => null,
        ]);

        // A donor currently inside the 90-day deferral window.
        $deferring = Donor::create([
            'name' => 'Meera Iyer',
            'email' => 'meera.iyer@example.com',
            'phone' => '9876501234',
            'blood_group' => 'B+',
            'date_of_birth' => '1999-11-03',
            'gender' => 'female',
            'city' => 'Mumbai',
            'address' => '7 Marine Drive',
            'weight_kg' => 58.2,
            'is_available' => true,
            'medical_notes' => null,
        ]);

        Donation::create([
            'donor_id' => $deferring->id,
            'donation_date' => now()->subDays(21)->toDateString(),
            'units' => 1,
            'camp' => 'City Hospital blood drive',
        ]);

        User::firstOrCreate(
            ['email' => 'admin@lifedrop.test'],
            ['name' => 'Admin', 'password' => Hash::make('password')],
        );
    }
}
