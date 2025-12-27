<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
  /**
   * Return a success response.
   */
  protected function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
  {
    return response()->json([
      'success' => true,
      'message' => $message,
      'data' => $data,
    ], $code);
  }

  /**
   * Return a created response.
   */
  protected function created($data = null, string $message = 'Created successfully'): JsonResponse
  {
    return $this->success($data, $message, 201);
  }

  /**
   * Return a no content response.
   */
  protected function noContent(string $message = 'Deleted successfully'): JsonResponse
  {
    return response()->json([
      'success' => true,
      'message' => $message,
    ], 200);
  }

  /**
   * Return an error response.
   */
  protected function error(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
  {
    return response()->json([
      'success' => false,
      'message' => $message,
      'errors' => $errors,
    ], $code);
  }
}
