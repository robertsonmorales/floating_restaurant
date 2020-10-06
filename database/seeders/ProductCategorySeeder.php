<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategories;
use Carbon\Carbon;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductCategories::insert([
        	[
        		'name' => 'Drinks',
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Food',
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Ice Cream',
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        ]);	
    }
}
