<?php

namespace App\Repositories;

use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];

            // calcula total a partir do payload antes de persistir
            $total = array_reduce($items, fn($sum, $i) => $sum + ($i['quantity'] * $i['price']), 0);

            $orderData = [
                'user_id' => $data['user_id'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'total_value' => $total,
            ];

            $order = Order::create($orderData);

            if (!empty($items)) {
                $normalized = array_map(fn($i) => [
                    'product_name' => $i['product_name'],
                    'quantity' => $i['quantity'],
                    'price' => $i['price'],
                ], $items);

                $order->items()->createMany($normalized);
            }

            return $order->load('items');
        });
    }

    public function paginateByStatus(string $status, int $perPage = 10): LengthAwarePaginator
    {
        return Order::where('status', $status)->with('items')->paginate($perPage);
    }

    public function find(int $id): ?Order
    {
        return Order::with('items')->find($id);
    }

    public function cancel(int $id): bool
    {
        $order = Order::find($id);
        if (!$order || $order->status === 'cancelled') {
            return false;
        }
        $order->status = 'cancelled';
        return $order->save();
    }
}