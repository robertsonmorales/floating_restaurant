<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\MenuTypes;

class MenuTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MenuTypes::insert([
        	[
        		'name' => 'Process by cook',
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Merchandise',
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Water Station',
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        ]);
    }
}
