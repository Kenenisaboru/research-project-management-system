<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Class ApiController
 *
 * Base controller providing standardized RFC-compliant JSON responses for API V1 endpoints.
 */
abstract class ApiController extends Controller
{
    /**
     * Return a standardized successful JSON response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $code
     * @return JsonResponse
     */
    protected function success(mixed $data = null, string $message = 'Operation successful', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => __($message),
            'data'    => $data,
            'errors'  => null,
        ], $code);
    }

    /**
     * Return a standardized error JSON response.
     *
     * @param  string  $message
     * @param  mixed  $errors
     * @param  int  $code
     * @return JsonResponse
     */
    protected function error(string $message = 'An error occurred', mixed $errors = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => __($message),
            'data'    => null,
            'errors'  => $errors,
        ], $code);
    }
}
