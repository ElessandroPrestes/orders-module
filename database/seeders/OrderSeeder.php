<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Cria 10 pedidos com 3 a 6 itens cada
        Order::factory()
            ->count(10)
            ->hasItems(fake()->numberBetween(3, 6))
            ->create();
    }
}
