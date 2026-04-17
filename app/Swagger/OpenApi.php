<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     title="Your Project API",
 *     version="1.0.0",
 *     description="API documentation"
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApi
{
    // This class is intentionally empty
}