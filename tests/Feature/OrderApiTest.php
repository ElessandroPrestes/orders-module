<?php

use App\Models\User;
use App\Models\Order;
use App\Jobs\SendOrderConfirmation;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\{postJson, getJson};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
    $this->usuario = User::factory()->create();
    Sanctum::actingAs($this->usuario);
});

it('cria um pedido com itens válidos e dispara job', function () {
    $dados = [
        'items' => [
            ['product_name' => 'Produto A', 'quantity' => 2, 'price' => 10.0],
            ['product_name' => 'Produto B', 'quantity' => 1, 'price' => 5.0],
        ]
    ];

    $resposta = postJson('/api/v1/orders', $dados);

    $resposta->assertCreated();
    expect((float) $resposta->json('data.total_value'))->toBe(25.0);
    Queue::assertPushed(SendOrderConfirmation::class);
});

it('retorna 422 ao enviar dados inválidos', function () {
    $dados = [
        'items' => [
            ['product_name' => '', 'quantity' => 0, 'price' => -1],
        ]
    ];

    $resposta = postJson('/api/v1/orders', $dados);
    $resposta->assertStatus(422);
});

it('lista pedidos filtrando por status', function () {
    Order::factory()->count(3)->create([
        'user_id' => $this->usuario->id,
        'status' => 'pending'
    ]);

    $resposta = getJson('/api/v1/orders?status=pending');
    $resposta->assertOk();
    expect($resposta->json('data'))->toHaveCount(3);
});

it('exibe detalhes de um pedido', function () {
    $pedido = Order::factory()->create(['user_id' => $this->usuario->id]);
    $resposta = getJson("/api/v1/orders/{$pedido->id}");
    $resposta->assertOk();
    expect($resposta->json('data.id'))->toBe($pedido->id); // ajustado para data.id
});

it('bloqueia cancelamento por usuário diferente', function () {
    $outroUsuario = User::factory()->create();
    $pedido = Order::factory()->create(['user_id' => $outroUsuario->id]);
    $resposta = postJson("/api/v1/orders/{$pedido->id}/cancel");
    $resposta->assertForbidden();
});