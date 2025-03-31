<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransferService
{
    public function transfer($payerId, $payeeId, $value)
    {
        $payer = User::findOrFail($payerId);
        $payee = User::findOrFail($payeeId);

        if ($payer->type === 'shopkeeper') {
            abort(403, 'Shopkeepers cannot transfer funds.');
        }

        if ($payer->balance < $value) {
            abort(400, 'Insufficient balance.');
        }

        $auth = Http::get('https://util.devi.tools/api/v2/authorize');
        if ($auth->json('message') !== 'Autorizado') {
            abort(403, 'Transfer not authorized.');
        }

        return DB::transaction(function () use ($payer, $payee, $value) {
            $payer->decrement('balance', $value);
            $payee->increment('balance', $value);

            $transaction = Transaction::create([
                'payer_id' => $payer->id,
                'payee_id' => $payee->id,
                'value'    => $value,
            ]);

            dispatch(new \App\Jobs\SendNotificationJob($payee->email));

            return $transaction;
        });
    }
}
