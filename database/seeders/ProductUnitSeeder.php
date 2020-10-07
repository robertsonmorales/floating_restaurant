<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductUnits;
use Carbon\Carbon;

class ProductUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductUnits::insert([
        	[
        		'name' => 'pack(s)',
        		'created_by' => 1,
                'created_at' => now()
        	],
        	[
        		'name' => 'pc(s)',
        		'created_by' => 1,
                'created_at' => now()
        	],
        	[
        		'name' => 'grams',
        		'created_by' => 1,
                'created_at' => now()
        	],
        	[
        		'name' => 'kg',
        		'created_by' => 1,
                'created_at' => now()
        	]
        ]);
    }
}
