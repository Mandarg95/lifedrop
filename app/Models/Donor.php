<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    use HasFactory;

    public const DEFERRAL_DAYS = 90;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'blood_group',
        'date_of_birth',
        'gender',
        'city',
        'address',
        'weight_kg',
        'is_available',
        'medical_notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_available' => 'boolean',
            'weight_kg' => 'float',
        ];
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function (Builder $query, string $term) {
            $query->where(function (Builder $query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('city', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        });
    }

    public function scopeFilter(Builder $query, ?string $bloodGroup, ?string $availability): Builder
    {
        return $query
            ->when($bloodGroup, fn (Builder $q) => $q->where('blood_group', $bloodGroup))
            ->when($availability === 'eligible', fn (Builder $q) => $q->where('is_available', true)
                ->whereNotExists(function ($q) {
                    $q->selectRaw('1')
                        ->from('donations')
                        ->whereColumn('donations.donor_id', 'donors.id')
                        ->where('donations.donation_date', '>=', now()->subDays(self::DEFERRAL_DAYS)->toDateString());
                }))
            ->when($availability === 'deferring', fn (Builder $q) => $q->where('is_available', true)
                ->whereExists(function ($q) {
                    $q->selectRaw('1')
                        ->from('donations')
                        ->whereColumn('donations.donor_id', 'donors.id')
                        ->where('donations.donation_date', '>=', now()->subDays(self::DEFERRAL_DAYS)->toDateString());
                }))
            ->when($availability === 'unavailable', fn (Builder $q) => $q->where('is_available', false));
    }

    public function latestDonation(): ?Donation
    {
        return $this->donations->sortByDesc('donation_date')->first();
    }

    public function lastDonationDate(): ?string
    {
        return $this->latestDonation()?->donation_date?->toDateString();
    }

    public function daysSinceLastDonation(): ?int
    {
        $last = $this->latestDonation()?->donation_date;

        return $last ? (int) $last->diffInDays(now()) : null;
    }

    public function nextEligibleDate(): ?string
    {
        $last = $this->latestDonation()?->donation_date;

        return $last ? $last->copy()->addDays(self::DEFERRAL_DAYS)->toDateString() : null;
    }

    public function isEligible(): bool
    {
        if (! $this->is_available) {
            return false;
        }

        $days = $this->daysSinceLastDonation();

        return $days === null || $days >= self::DEFERRAL_DAYS;
    }

    public function eligibilityLabel(): string
    {
        if (! $this->is_available) {
            return 'Unavailable';
        }

        $days = $this->daysSinceLastDonation();

        if ($days === null || $days >= self::DEFERRAL_DAYS) {
            return 'Eligible now';
        }

        return 'Eligible in '.(self::DEFERRAL_DAYS - $days).'d';
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}
