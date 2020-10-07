<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderSubStatus;
use Carbon\Carbon;

class OrderSubstatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrderSubStatus::insert([
        	[
	    		'name' => 'Pending',
	    		'color' => '#ff9800',
	    		'status' => 1,
	    		'created_by' => 1,
	    		'created_at' => Carbon::now()
	    	],
	    	[
	    		'name' => 'Confirmed',
	    		'color' => '#0e76bd',
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
	    	],
	    	[
	    		'name' => 'Served',
	    		'color' => '#3f51b5',
	    		'status' => 1,
	    		'created_by' => 1,
	    		'created_at' => Carbon::now()
	    	],
	    	[
	    		'name' => 'Cancelled',
	    		'color' => '#EA2034',
	    		'status' => 1,
	    		'created_by' => 1,
	    		'created_at' => Carbon::now()
	    	]
        ]);
    }
}
