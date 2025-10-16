<?php

namespace App\Docs\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="OrderResource",
 *     title="Order",
 *     description="Representação de um pedido",
 *     type="object",
 *     required={"id", "user_id", "status", "items", "created_at"},
 *     
 *     @OA\Property(property="id", type="integer", example=101),
 *     @OA\Property(property="user_id", type="integer", example=5),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"product_id", "quantity"},
 *             @OA\Property(property="product_id", type="integer", example=42),
 *             @OA\Property(property="quantity", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-16T14:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-10-16T14:30:00Z")
 * )
 */
class OrderResource {}
