<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('cria order_item via factory e via create; relacionamento funciona', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    // criar via factory
    $items = OrderItem::factory()->count(2)->create(['order_id' => $order->id]);
    $this->assertDatabaseCount('order_items', 2);

    // criar via create (exercita MassAssignment/Model::create)
    $item = OrderItem::create([
        'order_id' => $order->id,
        'product_name' => 'Teste X',
        'quantity' => 3,
        'price' => 7.5,
    ]);

    $this->assertDatabaseHas('order_items', ['id' => $item->id, 'product_name' => 'Teste X']);

    // relacionamentos
    $order->refresh();
    expect($order->items->contains(fn($i) => $i->id === $item->id))->toBeTrue();
    expect($item->order->id)->toBe($order->id);
});

it('ordem tem items e relacionamento funciona', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    $item = OrderItem::create([
        'order_id' => $order->id,
        'product_name' => 'Prod',
        'quantity' => 2,
        'price' => 5.0,
    ]);

    $this->assertDatabaseHas('order_items', ['id' => $item->id, 'order_id' => $order->id]);
    expect($order->items->first()->id)->toBe($item->id);
});

it('cria order_items e relaciona com order', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    $items =OrderItem::factory()->count(2)->create(['order_id' => $order->id]);

    $this->assertDatabaseCount('order_items', 2);

    expect($order->items->pluck('id')->sort()->values()->all())
        ->toEqual($items->pluck('id')->sort()->values()->all());
});