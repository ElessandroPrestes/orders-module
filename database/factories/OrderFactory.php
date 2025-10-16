<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_value' => 0,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
        ];
    }

    /**
     * Estado: pedido confirmado
     */
    public function confirmed(): static
    {
        return $this->state(fn() => ['status' => 'confirmed']);
    }

    /**
     * Estado: pedido cancelado
     */
    public function cancelled(): static
    {
        return $this->state(fn() => ['status' => 'cancelled']);
    }

    /**
     * Cria pedidos com itens automaticamente
     */
    public function withItems(int $count = 3): static
    {
        return $this->has(
            \App\Models\OrderItem::factory()->count($count),
            'items'
        );
    }
}
