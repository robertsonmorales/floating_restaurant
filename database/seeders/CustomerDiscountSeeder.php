<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerDiscount;
use Carbon\Carbon;

class CustomerDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerDiscount::insert([
        	[
        		'name' => 'Senior Citizen',
        		'percentage' => 20,
                'verification' => 1,
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'PWD',
        		'percentage' => 20,
                'verification' => 1,
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        ]);
    }
}
