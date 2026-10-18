<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'blood_group',
        'units_needed',
        'units_fulfilled',
        'hospital',
        'contact_name',
        'contact_phone',
        'needed_by',
        'urgency',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'needed_by' => 'date',
            'units_needed' => 'integer',
            'units_fulfilled' => 'integer',
        ];
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function (Builder $query, string $term) {
            $query->where(function (Builder $query) use ($term) {
                $query->where('patient_name', 'like', "%{$term}%")
                    ->orWhere('hospital', 'like', "%{$term}%")
                    ->orWhere('contact_phone', 'like', "%{$term}%");
            });
        });
    }

    public function scopeFilter(Builder $query, ?string $status, ?string $bloodGroup, ?string $urgency): Builder
    {
        return $query
            ->when($status, fn (Builder $q) => $q->where('status', $status))
            ->when($bloodGroup, fn (Builder $q) => $q->where('blood_group', $bloodGroup))
            ->when($urgency, fn (Builder $q) => $q->where('urgency', $urgency));
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function getProgressAttribute(): int
    {
        if ($this->units_needed <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->units_fulfilled / $this->units_needed) * 100));
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'pending'
            && $this->needed_by !== null
            && $this->needed_by->isPast();
    }
}
