<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpensesCategories;
use Carbon\Carbon;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ExpensesCategories::insert([
        	[
        		'name' => 'Deliveries',
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Dining Needs',
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Kitchen Needs',
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        ]);
    }
}
