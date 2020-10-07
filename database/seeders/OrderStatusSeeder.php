<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderStatus;
use Carbon\Carbon;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrderStatus::insert([
        	[
        		'name' => 'Unpaid',
                'color' => '#3e4044',
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	],
        	[
        		'name' => 'Paid',
                'color' => '#00e396',
        		'status' => 1,
        		'created_by' => 1,
        		'created_at' => Carbon::now()
        	]
        ]);
    }
}
