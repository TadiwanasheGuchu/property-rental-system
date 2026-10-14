<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin', 'property_manager', 'landlord');
    }

    public function view(User $user, Property $property): bool
    {
        return Property::visibleTo($user)->whereKey($property->getKey())->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin', 'property_manager');
    }

    public function update(User $user, Property $property): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasRole('property_manager')
            && $user->organization_id === $property->organization_id;
    }

    public function delete(User $user, Property $property): bool
    {
        return $this->update($user, $property);
    }
}
