<?php

namespace App\Services;

use App\Interfaces\OrderRepositoryInterface;
use App\Jobs\SendOrderConfirmation;
use App\Models\Order;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $repository
    ) {}

    public function create(array $data): Order
    {
        $order = $this->repository->create($data);
        SendOrderConfirmation::dispatch($order);
        return $order;
    }

    public function list(string $status)
    {
        return $this->repository->paginateByStatus($status);
    }

    public function detail(int $id): ?Order
    {
        return $this->repository->find($id);
    }

    public function cancel(int $id): bool
    {
        return $this->repository->cancel($id);
    }
}