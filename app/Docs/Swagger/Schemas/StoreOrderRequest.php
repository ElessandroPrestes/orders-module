<?php

namespace App\Docs\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="StoreOrderRequest",
 *     title="Store Order Request",
 *     description="Dados para criação de um pedido",
 *     type="object",
 *     required={"items"},
 *     
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         description="Lista de itens do pedido",
 *         @OA\Items(
 *             type="object",
 *             required={"product_id", "quantity"},
 *             @OA\Property(property="product_id", type="integer", example=42),
 *             @OA\Property(property="quantity", type="integer", example=3)
 *         )
 *     )
 * )
 */
class StoreOrderRequest {}
