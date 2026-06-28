<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        for ($i = 0; $i < 50; $i++) {
            $quantity = rand(1, 10);
            $price = rand(1000, 100000);
            DB::table('orders')->insert([
                'product_name' => 'Товар ' .$i,
                'quantity' => $quantity,
                'price' => $price,
                'created_at' => now()->subDays(rand(1, 30))->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        for ($i = 0; $i < 50; $i++) {
            $quantity = rand(1, 10);
            $price = rand(1000, 100000);
            DB::table('sales')->insert([
                'product_name' => 'Проданный товар ' .$i,
                'quantity' => $quantity,
                'price' => $price,
                'total_amount' => $quantity * $price,
                'created_at' => now()->subDays(rand(1, 30))->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        for ($i = 0; $i < 50; $i++) {
            $price = rand(1000, 100000);
            $source_type = ['Возврат', 'Продажа', 'Инвестиции'];
            DB::table('incomes')->insert([
                'source' => $source_type[array_rand($source_type)] .' №' .$i,
                'price' => $price,
                'created_at' => now()->subDays(rand(1, 30))->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        for ($i = 0; $i < 50; $i++) {
            $quantity = rand(1, 10);
            DB::table('stocks')->insert([
                'warehouse_name' => 'Склад ' .rand(1, 5),
                'product_name' => 'Товар ' .$i,
                'quantity' => $quantity,
                'created_at' => rand(0, 1) ? now()->toDateTimeString() : now()->subDay()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }
    }
}
