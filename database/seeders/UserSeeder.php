<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use Carbon\Carbon;
use Crypt;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'first_name' => Crypt::encryptString('admin'),
                'last_name' => Crypt::encryptString('admin'),
                'username' => 'admin',
                'contact_number' => Crypt::encryptString('09123456789'),
                'email' => Crypt::encryptString('admin@gmail.com'),
                'email_verified_at' => carbon::now(),
                'password' => Hash::make('admin'),
                'user_role' => 1,
                'created_by' => 1,
                'created_at' => carbon::now()
            ],
            [
                'first_name' => Crypt::encryptString('cashier'),
                'last_name' => Crypt::encryptString('cashier'),
                'username' => 'cashier',
                'contact_number' => Crypt::encryptString('09123456789'),
                'email' => Crypt::encryptString('user1@gmail.com'),
                'email_verified_at' => carbon::now(),
                'password' => Hash::make('cashier'),
                'user_role' => 2,
                'created_by' => 1,
                'created_at' => carbon::now()
            ],
            [
                'first_name' => Crypt::encryptString('manager'),
                'last_name' => Crypt::encryptString('manager'),
                'username' => 'manager',
                'contact_number' => Crypt::encryptString('09123456789'),
                'email' => Crypt::encryptString('user1@gmail.com'),
                'email_verified_at' => carbon::now(),
                'password' => Hash::make('manager'),
                'user_role' => 3,
                'created_by' => 1,
                'created_at' => carbon::now()
            ],
            [
                'first_name' => Crypt::encryptString('cook'),
                'last_name' => Crypt::encryptString('cook'),
                'username' => 'cook',
                'contact_number' => Crypt::encryptString('09123456789'),
                'email' => Crypt::encryptString('user1@gmail.com'),
                'email_verified_at' => carbon::now(),
                'password' => Hash::make('cook'),
                'user_role' => 4,
                'created_by' => 1,
                'created_at' => carbon::now()
            ]
        ]);
    }
}
