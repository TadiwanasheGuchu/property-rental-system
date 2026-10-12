<?php

namespace Database\Seeders;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Enums\UnitStatus;
use App\Enums\UnitType;
use App\Models\Organization;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $org      = Organization::where('slug', 'demo-property-management')->firstOrFail();
        $manager  = User::where('email', 'manager@example.test')->firstOrFail();
        $landlord = User::where('email', 'landlord@example.test')->firstOrFail();

        $property = Property::updateOrCreate(
            ['organization_id' => $org->id, 'name' => 'Ocean View Apartments'],
            [
                'owner_id'      => $landlord->id,
                'manager_id'    => $manager->id,
                'property_type' => PropertyType::ApartmentComplex,
                'address_line'  => '12 Strand Street',
                'city'          => 'Swakopmund',
                'region'        => 'Erongo',
                'country'       => 'Namibia',
                'description'   => 'Sea-facing apartment complex.',
                'status'        => PropertyStatus::Active,
            ]
        );

        // 12 units: floors 1-3, four per floor
        foreach (range(1, 12) as $i) {
            $floor  = intdiv($i - 1, 4) + 1;
            $letter = chr(64 + (($i - 1) % 4) + 1); // A-D
            $number = $floor . $letter;              // 1A, 1B ... 3D

            $property->units()->updateOrCreate(
                ['unit_number' => $number],
                [
                    'unit_type'    => UnitType::Apartment,
                    'bedrooms'     => $i % 3 === 0 ? 3 : 2,
                    'bathrooms'    => 1.0,
                    'floor'        => $floor,
                    'monthly_rent' => 6500 + ($floor * 250),
                    'deposit'      => 6500 + ($floor * 250),
                    'status'       => UnitStatus::Available,
                ]
            );
        }
    }
}
