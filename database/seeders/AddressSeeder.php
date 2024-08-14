<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $addresses = [
            [
                'id'             => 1,
                'name'          => 'Maadi',
            ],
            [
                'id'             => 2,
                'name'          => 'Dokki',
            ],
            [
                'id'             => 3,
                'name'          => 'Madent nasr',
            ],
        ];

        Address::insert($addresses);
    }
}
