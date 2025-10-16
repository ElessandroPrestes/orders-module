<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_name' => $this->faker->words(2, true),
            'quantity' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
