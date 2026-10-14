<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'property_type' => $this->property_type->value,
            'address_line'  => $this->address_line,
            'city'          => $this->city,
            'region'        => $this->region,
            'country'       => $this->country,
            'description'   => $this->description,
            'status'        => $this->status->value,
            'owner'         => $this->whenLoaded('owner', fn() => $this->owner
                ? ['id' => $this->owner->id, 'name' => $this->owner->name] : null),
            'manager'       => $this->whenLoaded('manager', fn() => $this->manager
                ? ['id' => $this->manager->id, 'name' => $this->manager->name] : null),
            'units_count'   => $this->whenCounted('units'),
            'created_at'    => $this->created_at,
        ];
    }
}
