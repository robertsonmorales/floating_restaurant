<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Stock::insert([
        	[
        		'product_id' => 1,
        		'product_name' => 'Coke 1.5',
        		'product_category_id' => 1,
        		'product_category_name' => 'Drinks',
        		'unit' => 2,
                'status' => 1,
        		'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
        	],
        	[
        		'product_id' => 2,
        		'product_name' => 'Tapa',
        		'product_category_id' => 2,
        		'product_category_name' => 'Food',
        		'unit' => 1,
                'status' => 1,
        		'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
        	],
        	[
        		'product_id' => 3,
        		'product_name' => 'Cream Cone Rocky Road',
        		'product_category_id' => 3,
        		'product_category_name' => 'Ice Cream',
        		'unit' => 2,
                'status' => 1,
        		'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
        	],
        	[
        		'product_id' => 4,
        		'product_name' => 'Egg',
        		'product_category_id' => 3,
        		'product_category_name' => 'Food',
        		'unit' => 2,
                'status' => 1,
        		'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
        	],
        	[
        		'product_id' => 5,
        		'product_name' => 'Chicken',
        		'product_category_id' => 2,
        		'product_category_name' => 'Food',
        		'unit' => 1,
                'status' => 1,
        		'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
        	],
        	[
        		'product_id' => 3,
        		'product_name' => 'Hotdog',
        		'product_category_id' => 2,
        		'product_category_name' => 'Food',
        		'unit' => 2,
                'status' => 1,
        		'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
        	]
        ]);
    }
}
