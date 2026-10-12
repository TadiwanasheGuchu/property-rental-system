<?php

namespace App\Models;

use App\Enums\UnitStatus;
use App\Enums\UnitType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'property_id',
    'unit_number',
    'unit_type',
    'bedrooms',
    'bathrooms',
    'floor',
    'monthly_rent',
    'deposit',
    'status',
    'description',
])]
class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'unit_type'    => UnitType::class,
            'status'       => UnitStatus::class,
            'monthly_rent' => 'decimal:2',
            'deposit'      => 'decimal:2',
            'bathrooms'    => 'decimal:1',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
