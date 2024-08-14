<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sources = [
            [
                'id'             => 1,
                'name'          => 'Facebook',
            ],
            [
                'id'             => 2,
                'name'          => 'Instagram',
            ],
            [
                'id'             => 3,
                'name'          => 'Walk-in',
            ],
            [
                'id'             => 4,
                'name'          => 'Referral',
            ],
        ];

        Source::insert($sources);
    }
}
