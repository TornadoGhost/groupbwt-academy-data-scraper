<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponseHelper
{
    public function unauthorizedResponse(string $message = 'This action is unauthorized.'): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 403);
    }

    protected function successResponse($message = 'Operation successful', $code = 200, $data = []): JsonResponse
    {
        if ($data) {
            return response()->json([
                'status' => 'Success',
                'message' => $message,
                'data' => $data
            ], $code);
        }
        return response()->json([
            'status' => 'Success',
            'message' => $message
        ], $code);
    }

    protected function errorResponse($message = 'Operation failed', $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
        ], $code);
    }
}
