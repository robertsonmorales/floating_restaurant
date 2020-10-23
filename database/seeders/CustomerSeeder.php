<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customers;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customers::insert([
        	'name' => 'unknown',
        	'created_by' => 2,
        	'created_at' => Carbon::now()
        ]);
    }
}
