<?php

namespace Tests\Feature;

use App\Models\User;
use App\Jobs\SendNotificationJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_transfer_fails_with_insufficient_balance()
    {
        $payer = User::factory()->create(['type' => 'common', 'balance' => 10]);
        $payee = User::factory()->create();

        Http::fake(['https://util.devi.tools/api/v2/authorize' => Http::response(['message' => 'Autorizado'], 200)]);

        $response = $this->postJson('/api/transfer', [
            'value' => 50,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Insufficient balance.']);
    }

    public function test_shopkeeper_cannot_initiate_transfer()
    {
        $payer = User::factory()->create(['type' => 'shopkeeper', 'balance' => 500]);
        $payee = User::factory()->create();

        Http::fake(['https://util.devi.tools/api/v2/authorize' => Http::response(['message' => 'Autorizado'], 200)]);

        $response = $this->postJson('/api/transfer', [
            'value' => 50,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Shopkeepers cannot transfer funds.']);
    }

    public function test_transfer_fails_when_authorization_is_denied()
    {
        $payer = User::factory()->create(['type' => 'common', 'balance' => 500]);
        $payee = User::factory()->create();

        Http::fake(['https://util.devi.tools/api/v2/authorize' => Http::response(['message' => 'Não autorizado'], 200)]);

        $response = $this->postJson('/api/transfer', [
            'value' => 50,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Transfer not authorized.']);
    }

    public function test_transfer_fails_with_invalid_fields()
    {
        $payer = User::factory()->create();
        $payee = User::factory()->create();

        $response = $this->postJson('/api/transfer', [
            'value' => -10,
            'payer' => $payer->id,
            'payee' => $payer->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['value', 'payee']);
    }

    public function test_transfer_dispatches_notification_job()
    {
        Queue::fake();

        $payer = User::factory()->create(['type' => 'common', 'balance' => 500]);
        $payee = User::factory()->create();

        Http::fake(['https://util.devi.tools/api/v2/authorize' => Http::response(['message' => 'Autorizado'], 200)]);

        $this->postJson('/api/transfer', [
            'value' => 100,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        Queue::assertPushed(SendNotificationJob::class);
    }
}
