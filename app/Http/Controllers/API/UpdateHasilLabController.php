<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\UpdateHasilLabRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class UpdateHasilLabController
{
    public function __invoke(UpdateHasilLabRequest $request): JsonResponse
    {
        try {

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Data berhasil diupdate',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
