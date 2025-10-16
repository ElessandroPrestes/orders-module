<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('retorna 200 e os dados do usuário autenticado', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = getJson('/api/user');

    $response->assertOk();
    expect($response->json('id'))->toBe($user->id);
    expect($response->json('email'))->toBe($user->email);
});

it('retorna 401 quando não autenticado', function () {
    $response = getJson('/api/user');

    $response->assertUnauthorized();
});