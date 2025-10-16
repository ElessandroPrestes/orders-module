<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service
    ) {}

    /**
     * @OA\Post(
     *     path="/api/v1/orders",
     *     summary="Cria um novo pedido",
     *     tags={"Pedidos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreOrderRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pedido criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
     *     )
     * )
     */
    public function store(StoreOrderRequest $request): OrderResource
    {
        $order = $this->service->create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'items' => $request->validated()['items'],
        ]);

        return new OrderResource($order);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders",
     *     summary="Lista pedidos do usuário autenticado",
     *     tags={"Pedidos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="pending")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pedidos",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/OrderResource"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $orders = $this->service->list($request->get('status', 'pending'));
        return OrderResource::collection($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{id}",
     *     summary="Exibe detalhes de um pedido",
     *     tags={"Pedidos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do pedido",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     )
     * )
     */
    public function show(int $id)
    {
        $order = $this->service->detail($id);
        return new OrderResource($order);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/orders/{id}/cancel",
     *     summary="Cancela um pedido do usuário autenticado",
     *     tags={"Pedidos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cancelamento realizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Ação não autorizada"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao cancelar pedido"
     *     )
     * )
     */
    public function cancel(int $id)
    {
        $order = $this->service->detail($id);

        if (! $order) {
            return response()->json(['message' => 'Pedido não encontrado'], 404);
        }

        // valida ownership explicitamente
        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Ação não autorizada'], 403);
        }

        try {
            // tenta cancelar e retorna o resultado
            $success = $this->service->cancel($id);
            return response()->json(['success' => (bool) $success], 200);
        } catch (\Throwable $e) {
            // log para diagnóstico
            Log::error('Erro ao cancelar pedido', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Erro interno ao cancelar pedido'], 500);
        }
    }
}