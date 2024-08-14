<?php

namespace Database\Seeders;

use App\Models\Action;
use Illuminate\Database\Seeder;

class ActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions = [
            [
                'id'             => 1,
                'name'          => 'Tour',
            ],
            [
                'id'             => 2,
                'name'          => 'Invitaion',
            ],
            [
                'id'             => 3,
                'name'          => 'Whatsapp',
            ],
        ];

        Action::insert($actions);
    }
}
