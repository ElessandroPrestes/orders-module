<?php

use App\Models\Order;
use App\Jobs\SendOrderConfirmation;
use App\Services\OrderService;
use App\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\Queue;
use Mockery;

uses(Tests\TestCase::class)->group('unit');

it('service cria pedido e dispara job', function () {
    Queue::fake();

    // evitar criação de users/tabelas durante unit test
    $order = Order::factory()->make(['user_id' => 1]);

    $repo = Mockery::mock(OrderRepositoryInterface::class);
    $repo->shouldReceive('create')->once()->with(Mockery::type('array'))->andReturn($order);

    $service = new OrderService($repo);
    $result = $service->create(['user_id' => 1, 'items' => []]);

    Queue::assertPushed(SendOrderConfirmation::class);
    expect($result)->toBe($order);

    Mockery::close();
});