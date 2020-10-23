<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            MenuCategorySeeder::class,
            MenuTypeSeeder::class,
            ProductCategorySeeder::class,
            ProductUnitSeeder::class,
            ProductSeeder::class,
            OrderStatusSeeder::class,
            OrderSubstatusSeeder::class,
            ExpenseCategorySeeder::class,
            CustomerDiscountSeeder::class,
            EmployeePositionSeeder::class,
            StockSeeder::class,
            CustomerSeeder::class,
            OrderSeeder::class
        ]);
    }
}
