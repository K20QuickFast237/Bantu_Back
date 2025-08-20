<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

trait ApiResponseHandler
{
    /**
     * Exécute une action dans une transaction avec gestion des erreurs et réponse JSON.
     *
     * @param callable $callback
     * @param int $successCode Code HTTP succès (default 200)
     * @return JsonResponse
     */
    public function handleApi(callable $callback, int $successCode = 200): JsonResponse
    {
        DB::beginTransaction();

        try {
            $result = $callback();

            DB::commit();

            return response()->json($result, $successCode);
        } catch (Exception $e) {
            DB::rollBack();

            // Log l'erreur si besoin (ici simple log)
            \Log::error($e);

            return response()->json([
                'error' => 'Une erreur est survenue',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Réponse JSON simple sans transaction, avec gestion d'erreur.
     *
     * @param callable $callback
     * @param int $successCode
     * @return JsonResponse
     */
    public function handleApiNoTransaction(callable $callback, int $successCode = 200): JsonResponse
    {
        try {
            $result = $callback();

            return response()->json($result, $successCode);
        } catch (Exception $e) {
            \Log::error($e);

            return response()->json([
                'error' => 'Une erreur est survenue',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
