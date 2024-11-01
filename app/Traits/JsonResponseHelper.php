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

    protected function successResponseWithPagination($resource, $message = 'Operation successful', $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $resource->resolve(),
            'links' => $resource->response()->getData()->links,
            'meta' => $resource->response()->getData()->meta,
        ], $code);
    }

    protected function errorResponse($message = 'Operation failed', $code = 400, $errors = ''): JsonResponse
    {
        if ($errors) {
            $response = response()->json([
                'status' => 'Error',
                'message' => $message,
                'data' => $errors,
            ], $code);
        } else {
            $response = response()->json([
                'status' => 'Error',
                'message' => $message,
            ], $code);
        }

        return $response;
    }
}
