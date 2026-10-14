<?php

namespace App\Http\Requests;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Property;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($this->isMethod('POST')) {
            return $user->can('create', Property::class);
        }

        // Outside the user's scope (or nonexistent) is a 404, decided before any validation runs.
        $property = Property::visibleTo($user)->find($this->route('property'));
        abort_if($property === null, 404);

        return $user->can('update', $property);
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('POST');
        $orgId    = $this->targetOrganizationId();

        return [
            // Only a super admin chooses the organization, and only on create.
            'organization_id' => $isCreate && $this->user()->hasRole('super_admin')
                ? ['required', 'integer', 'exists:organizations,id']
                : ['prohibited'],

            'name' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, Closure $fail) use ($orgId) {
                    $query = Property::where('organization_id', $orgId)
                        ->whereRaw('lower(name) = ?', [mb_strtolower($value)]);

                    if ($id = $this->route('property')) {
                        $query->where('id', '!=', $id);
                    }

                    if ($query->exists()) {
                        $fail('A property with this name already exists.');
                    }
                },
            ],
            'property_type' => ['required', Rule::enum(PropertyType::class)],
            'address_line'  => ['required', 'string', 'max:255'],
            'city'          => ['required', 'string', 'max:120'],
            'region'        => ['required', 'string', 'max:120'],
            'country'       => ['nullable', 'string', 'max:120'],
            'description'   => ['nullable', 'string', 'max:5000'],
            'status'        => ['nullable', Rule::enum(PropertyStatus::class)],

            'owner_id'   => ['nullable', 'integer', $this->userHasRoleInOrg('landlord', $orgId)],
            'manager_id' => ['nullable', 'integer', $this->userHasRoleInOrg('property_manager', $orgId)],
        ];
    }

    /**
     * The organization this property will belong to: the chosen one (super admin,
     * create), the user's own (manager, create), or the existing property's (update).
     */
    private function targetOrganizationId(): ?int
    {
        if ($this->isMethod('POST')) {
            return $this->user()->hasRole('super_admin')
                ? (int) $this->input('organization_id')
                : $this->user()->organization_id;
        }

        // Look the property up through the visibility scope, so validation
        // can never leak details of a record the user is not allowed to see.
        return Property::visibleTo($this->user())
            ->whereKey($this->route('property'))
            ->value('organization_id');
    }

    /**
     * The referenced user must be active, in the same organization, and hold the given role.
     */
    private function userHasRoleInOrg(string $role, ?int $orgId): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($role, $orgId) {
            $valid = User::whereKey($value)
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->whereHas('roles', fn($q) => $q->where('name', $role))
                ->exists();

            if (! $valid) {
                $fail("The selected {$attribute} is invalid.");
            }
        };
    }
}
