<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin'       => 'Super Admin',
            'property_manager'  => 'Property Manager',
            'landlord'          => 'Landlord',
            'tenant'            => 'Tenant',
            'maintenance_staff' => 'Maintenance Staff',
        ];

        foreach ($roles as $name => $label) {
            Role::updateOrCreate(['name' => $name], ['label' => $label]);
        }
    }
}
