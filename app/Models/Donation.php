<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'donation_date',
        'units',
        'camp',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'donation_date' => 'date',
            'units' => 'integer',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function (Builder $query, string $term) {
            $query->where(function (Builder $query) use ($term) {
                $query->where('camp', 'like', "%{$term}%")
                    ->orWhereHas('donor', fn (Builder $q) => $q
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%"));
            });
        });
    }

    public function scopeFilter(Builder $query, ?string $bloodGroup, ?string $period): Builder
    {
        return $query
            ->when($bloodGroup, fn (Builder $q) => $q->whereHas('donor', fn (Builder $q) => $q->where('blood_group', $bloodGroup)))
            ->when($period === '30', fn (Builder $q) => $q->where('donation_date', '>=', now()->subDays(30)))
            ->when($period === '90', fn (Builder $q) => $q->where('donation_date', '>=', now()->subDays(90)))
            ->when($period === '365', fn (Builder $q) => $q->where('donation_date', '>=', now()->subDays(365)));
    }
}
