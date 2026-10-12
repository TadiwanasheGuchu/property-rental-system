<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $org = Organization::updateOrCreate(
            ['slug' => 'demo-property-management'],
            ['name' => 'Demo Property Management', 'email' => 'info@demo-pm.test']
        );

        // [role, name, email, organization_id]
        $users = [
            ['super_admin',       'Super Admin',   'admin@example.test',       null],
            ['property_manager',  'Pat Manager',   'manager@example.test',     $org->id],
            ['landlord',          'Lee Landlord',  'landlord@example.test',    $org->id],
            ['tenant',            'Tina Tenant',   'tenant@example.test',      $org->id],
            ['maintenance_staff', 'Mo Maintenance', 'maintenance@example.test', $org->id],
        ];

        foreach ($users as [$role, $name, $email, $orgId]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'            => $name,
                    'password'        => 'password', // dev only; hashed by the model cast
                    'organization_id' => $orgId,
                    'is_active'       => true,
                ]
            );

            $user->roles()->syncWithoutDetaching([
                Role::where('name', $role)->value('id'),
            ]);
        }

        // Must run last: it needs the organization and users above.
        $this->call(PropertySeeder::class);
    }
}
