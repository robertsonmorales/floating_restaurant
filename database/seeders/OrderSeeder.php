<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Orders;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Orders::insert([
        	'customer_id' => 1, // default when migrated
            'transaction_no' => 000001,
            'status' => 1, // unpaid
        	'created_by' => 2, // cashier
        	'created_at' => Carbon::now()
        ]);
    }
}
