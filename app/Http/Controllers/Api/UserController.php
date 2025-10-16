<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/user",
 *     summary="Retorna o usuário autenticado",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Usuário autenticado retornado com sucesso"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Não autenticado"
 *     )
 * )
 */
class UserController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user());
    }
}
