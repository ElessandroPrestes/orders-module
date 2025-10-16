<?php

namespace App\Interfaces;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;
    public function paginateByStatus(string $status, int $perPage = 10): LengthAwarePaginator;
    public function find(int $id): ?Order;
    public function cancel(int $id): bool;
}