<?php

use Illuminate\Http\JsonResponse;

public function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
{
    $response = [
        'success' => true,
        'message' => $message,
    ];

    if (!is_null($data)) {
        $response['data'] = $data;
    }

    return response()->json($response, $code);
}

public function created($data = null, string $message = 'Created successfully'): JsonResponse
{
    return $this->success($data, $message, 201);
}

public function paginated($paginator, string $message = 'Success'): JsonResponse
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data'    => $paginator->items(),
        'meta'    => [
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
            'from'         => $paginator->firstItem(),
            'to'           => $paginator->lastItem(),
        ],
        'links' => [
            'first' => $paginator->url(1),
            'last'  => $paginator->url($paginator->lastPage()),
            'prev'  => $paginator->previousPageUrl(),
            'next'  => $paginator->nextPageUrl(),
        ],
    ], 200);
}

public function error(string $message, int $code = 400, $errors = null): JsonResponse
{
    $response = [
        'success' => false,
        'message' => $message,
    ];

    if (!is_null($errors)) {
        $response['errors'] = $errors;
    }

    return response()->json($response, $code);
}

public function notFound(string $message = 'Resource not found'): JsonResponse
{
    return $this->error($message, 404);
}

public function forbidden(string $message = 'Forbidden'): JsonResponse
{
    return $this->error($message, 403);
}

public function unauthorized(string $message = 'Unauthorized'): JsonResponse
{
    return $this->error($message, 401);
}

public function validationError($errors, string $message = 'Validation failed'): JsonResponse
{
    return $this->error($message, 422, $errors);
}

public function noContent(): JsonResponse
{
    return response()->json(null, 204);
}
