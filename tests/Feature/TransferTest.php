<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TransferTest extends TestCase
{

    public function test_user_can_transfer_money()
{
    $payer = User::factory()->create(['type' => 'common', 'balance' => 200]);
    $payee = User::factory()->create(['type' => 'shopkeeper']);

    Http::fake([
        'https://util.devi.tools/api/v2/authorize' => Http::response(['message' => 'Autorizado'], 200),
    ]);

    $response = $this->postJson('/api/transfer', [
        'value' => 50,
        'payer' => $payer->id,
        'payee' => $payee->id,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('transactions', ['payer_id' => $payer->id, 'payee_id' => $payee->id]);
}

}
