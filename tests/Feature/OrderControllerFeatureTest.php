<?php

use App\Models\User;
use App\Models\Order;
use App\Jobs\SendOrderConfirmation;
use App\Services\OrderService;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\{postJson, getJson};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

it('cria pedido válido e dispara job', function () {
    $payload = [
        'items' => [
            ['product_name' => 'A', 'quantity' => 2, 'price' => 10.0],
            ['product_name' => 'B', 'quantity' => 1, 'price' => 5.0],
        ],
    ];

    $response = postJson('/api/v1/orders', $payload);
    $response->assertCreated();
    expect((float) $response->json('data.total_value'))->toBe(25.0);
    Queue::assertPushed(SendOrderConfirmation::class);
});

it('valida dados inválidos retorna 422', function () {
    $payload = ['items' => [['product_name' => '', 'quantity' => 0, 'price' => -1]]];
    $response = postJson('/api/v1/orders', $payload);
    $response->assertStatus(422);
});

it('lista pedidos por status', function () {
    Order::factory()->count(2)->create(['user_id' => $this->user->id, 'status' => 'pending']);
    $response = getJson('/api/v1/orders?status=pending');
    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
});

it('mostra detalhes do pedido', function () {
    $order = Order::factory()->create(['user_id' => $this->user->id]);
    $response = getJson("/api/v1/orders/{$order->id}");
    $response->assertOk();
    expect($response->json('data.id'))->toBe($order->id);
});

it('cancela pedido do mesmo usuário', function () {
    $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);
    $response = postJson("/api/v1/orders/{$order->id}/cancel");
    $response->assertOk();
    expect($response->json('success'))->toBeTrue();
});

it('nao permite cancelar pedido de outro usuário', function () {
    $other = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $other->id, 'status' => 'pending']);
    $response = postJson("/api/v1/orders/{$order->id}/cancel");
    $response->assertForbidden();
});

it('retorna success false se já cancelado', function () {
    $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => 'cancelled']);
    $response = postJson("/api/v1/orders/{$order->id}/cancel");
    $response->assertOk();
    expect($response->json('success'))->toBeFalse();
});

it('retorna 404 ao cancelar pedido inexistente', function () {
    $mock = Mockery::mock(OrderService::class);
    $mock->shouldReceive('detail')->with(999)->once()->andReturn(null);
    app()->instance(OrderService::class, $mock);

    $res = postJson('/api/v1/orders/999/cancel');
    $res->assertStatus(404);
    Mockery::close();
});

it('retorna 500 quando service lança exceção ao cancelar', function () {
    $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);

    $mock = Mockery::mock(OrderService::class);
    $mock->shouldReceive('detail')->with($order->id)->once()->andReturn($order);
    $mock->shouldReceive('cancel')->with($order->id)->once()->andThrow(new \Exception('boom'));
    app()->instance(OrderService::class, $mock);

    $res = postJson("/api/v1/orders/{$order->id}/cancel");
    $res->assertStatus(500);
    Mockery::close();
});