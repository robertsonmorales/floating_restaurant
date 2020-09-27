<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuCategories;
use Carbon\Carbon;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MenuCategories::insert([
        	[
        		'name' => 'Add-ons',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Appetizer',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Beverages',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Coffee',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Creamline',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Dessert',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Liquor',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Main course',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'MilkShake/Juice',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Noodles',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Pasta',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Rice',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Sandwiches',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Silog',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Sizzling',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Styro (Take out)',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'v5',
                'status' => 1,
        		'created_at' => Carbon::now()
        	],
        ]);
    }
}
