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
}
