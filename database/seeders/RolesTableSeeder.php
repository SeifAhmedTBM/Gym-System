<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id'    => 1,
                'title' => 'Admin',
            ],
            [
                'id'    => 2,
                'title' => 'Trainer',
            ],
            [
                'id'    => 3,
                'title' => 'Sales',
            ],
            [
                'id'    => 4,
                'title' => 'Receptionist',
            ],
            [
                'id'    => 5,
                'title' => 'Member',
            ],
        ];

        Role::insert($roles);
    }
}
