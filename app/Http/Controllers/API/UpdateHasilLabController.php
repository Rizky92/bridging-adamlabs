<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\UpdateHasilLabRequest;
use Illuminate\Http\JsonResponse;

class UpdateHasilLabController
{
    public function __invoke(UpdateHasilLabRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Data berhasil diupdate',
        ]);
    }
}
