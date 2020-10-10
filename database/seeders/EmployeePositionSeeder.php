<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\EmployeePositions;

class EmployeePositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmployeePositions::insert([
            [
                'name' => 'Server',
                'status' => 1,
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Cashier',
                'status' => 1,
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Manager',
                'status' => 1,
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],  
            [
                'name' => 'Dish Washer',
                'status' => 1,
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Security Guard',
                'status' => 1,
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Cook',
                'status' => 1,
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Assistant Cook',
                'status' => 1,
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
        ]);
    }
}
