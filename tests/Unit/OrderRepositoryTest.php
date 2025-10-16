<?php

use App\Models\User;
use App\Repositories\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('persiste pedido com items e calcula total corretamente', function () {
    $user = User::factory()->create();

    $repo = new OrderRepository();

    $data = [
        'user_id' => $user->id,
        'status' => 'pending',
        'items' => [
            ['product_name' => 'Produto A', 'quantity' => 2, 'price' => 10.0],
            ['product_name' => 'Produto B', 'quantity' => 1, 'price' => 5.0],
        ],
    ];

    $order = $repo->create($data);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'user_id' => $user->id,
        'status' => 'pending',
        'total_value' => 25.0,
    ]);

    $this->assertDatabaseCount('order_items', 2);
});