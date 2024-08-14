<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $serviceTypes = [
            [
                'id'             => 1,
                'name'          => 'Open Gym',
            ],
            [
                'id'             => 2,
                'name'          => 'PT',
            ],
            [
                'id'             => 3,
                'name'          => 'Classes',
            ],
        ];

        ServiceType::insert($serviceTypes);
    }
}
