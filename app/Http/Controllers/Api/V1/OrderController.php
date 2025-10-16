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

    public function store(StoreOrderRequest $request): OrderResource
    {
        $order = $this->service->create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'items' => $request->validated()['items'],
        ]);

        return new OrderResource($order);
    }

    public function index(Request $request)
    {
        $orders = $this->service->list($request->get('status', 'pending'));
        return OrderResource::collection($orders);
    }

    public function show(int $id)
    {
        $order = $this->service->detail($id);
        return new OrderResource($order);
    }

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