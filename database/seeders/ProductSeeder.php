<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Products;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Products::insert([
        	[
        		'name' => 'Coke 1.5',
        		'product_categories_id' => 1,
                'unit' => 2,
                'minimum_stocks' => 2,
        		'status' => 1,
        		'created_by' => 1,
                'created_at' => now()
        	],
        	[
        		'name' => 'Tapa',
        		'product_categories_id' => 2,
                'unit' => 1,
                'minimum_stocks' => 10,
        		'status' => 1,
        		'created_by' => 1,
                'created_at' => now()
        	],
        	[
        		'name' => 'Cream Cone Rocky Road',
        		'product_categories_id' => 3,
                'unit' => 2,
                'minimum_stocks' => 5,
        		'status' => 1,
        		'created_by' => 1,
                'created_at' => now()
        	],
        	[
        		'name' => 'Egg',
        		'product_categories_id' => 2,
                'unit' => 2,
                'minimum_stocks' => 30,
        		'status' => 1,
        		'created_by' => 1,
                'created_at' => now()
        	],
            [
                'name' => 'Chicken',
                'product_categories_id' => 2,
                'unit' => 1,
                'minimum_stocks' => 5,
                'status' => 1,
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Hotdog',
                'product_categories_id' => 2,
                'unit' => 2,
                'minimum_stocks' => 5,
                'status' => 1,
                'created_by' => 1,
                'created_at' => now()
            ],
        ]);
    }
}
