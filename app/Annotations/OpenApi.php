<?php

namespace App\Annotations;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "REST API for myAgenci.ai Mobile Application.",
    title: "myAgenci.ai Mobile App API"
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: "API Server")]
#[OA\SecurityScheme(securityScheme: "sanctum", type: "http", scheme: "bearer", bearerFormat: "JWT")]
#[OA\Tag(name: "Auth", description: "Mobile authentication endpoints")]
class OpenApi {}