<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Services\TransferService;

/**
 * @OA\Post(
 *     path="/api/transfer",
 *     summary="Realiza uma transferência entre usuários",
 *     tags={"Transferências"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"value","payer","payee"},
 *             @OA\Property(property="value", type="number", format="float", example=100.0),
 *             @OA\Property(property="payer", type="integer", example=1),
 *             @OA\Property(property="payee", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Transferência realizada com sucesso"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Saldo insuficiente"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Transferência não autorizada ou proibida"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erro de validação"
 *     )
 * )
 */

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
