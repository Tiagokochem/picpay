<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Services\TransferService;

class TransferController extends Controller
{
    public function __construct(private TransferService $service) {}

    public function __invoke(TransferRequest $request)
    {
        $transfer = $this->service->transfer(
            $request->payer,
            $request->payee,
            $request->value
        );

        return response()->json($transfer, 201);
    }
}
