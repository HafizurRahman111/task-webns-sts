<?php

namespace App\Traits;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

trait ResponseTrait
{
    /**
     * Return a success response.
     */
    protected function successResponse($data, $status = 200, $message = null)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return an error response.
     */
    protected function errorResponse($message, \Exception $e = null, $status = 500)
    {
        Log::error($message, [
            'exception' => $e,
            'request' => request()->all(),
        ]);
        return response()->json(['success' => false, 'error' => $message,], $status);
    }

    /**
     * Return a validation error response.
     */
    protected function validationErrorResponse(ValidationException $e)
    {
        return response()->json(['success' => false, 'errors' => $e->errors()], 422);
    }
}