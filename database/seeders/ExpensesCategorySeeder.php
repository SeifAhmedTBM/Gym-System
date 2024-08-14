<?php

namespace Database\Seeders;

use App\Models\ExpensesCategory;
use Illuminate\Database\Seeder;

class ExpensesCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $expensesCategories = [
            [
                'id'             => 1,
                'name'          => 'Rent',
            ],
            [
                'id'             => 2,
                'name'          => 'Utilities',
            ],
            [
                'id'             => 3,
                'name'          => 'ADSL',
            ],
        ];

        ExpensesCategory::insert($expensesCategories);
    }
}
