<?php

use App\Jobs\SendOrderConfirmation;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail; // ou Notification, dependendo do job
use Illuminate\Support\Facades\Notification;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('job executa sem erro (fakes se necessário)', function () {
    Mail::fake(); // adapte se o job usa Notification::send ou Mail::to

    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    $job = new SendOrderConfirmation($order);

    $job->handle();

    $this->assertTrue(true);
});

it('executa job SendOrderConfirmation sem lançar exceção', function () {
    // fakes para cobrir envios de email/notification usados no job
    Mail::fake();
    Notification::fake();

    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    $job = new SendOrderConfirmation($order);

    // chamar handle (se o job tem dependências, os fakes evitam side effects)
    $job->handle();

    // asserts mínimos para garantir execução
    $this->assertTrue(true);
});