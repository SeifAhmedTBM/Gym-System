<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            [
                'id'                => 1,
                'name'              => 'Thinking',
                'color'             => "#DEDEDE",
                'default_next_followup_days' => 1,
                'need_followup'     => 1,
            ],
            [
                'id'             => 2,
                'name'          => 'Comming',
                'color'             => "#DEDEDE",
                'default_next_followup_days' => 1,
                'need_followup'     => 1,
            ],
            [
                'id'                => 3,
                'name'              => 'Not-interested',
                'color'             => "#DEDEDE",
                'default_next_followup_days' => 1,
                'need_followup'     => 1,
            ],
        ];

        Status::insert($statuses);
    }
}
