<?php

namespace App\Models;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

#[Fillable([
    'organization_id',
    'owner_id',
    'manager_id',
    'name',
    'property_type',
    'address_line',
    'city',
    'region',
    'country',
    'description',
    'status',
])]
class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'property_type' => PropertyType::class,
            'status'        => PropertyStatus::class,
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Restrict a query to the properties this user is allowed to see.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('property_manager')) {
            return $query->where('organization_id', $user->organization_id);
        }

        if ($user->hasRole('landlord')) {
            return $query
                ->where('organization_id', $user->organization_id)
                ->where('owner_id', $user->id);
        }

        return $query->whereRaw('1 = 0'); // tenants, maintenance staff: nothing
    }
}
