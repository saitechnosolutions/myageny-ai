<?php

namespace App\Http\Controllers\App;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "AuthUser", type: "object", description: "Authenticated user profile",
    properties: [
        new OA\Property(property: "id",            type: "integer", example: 1),
        new OA\Property(property: "name",          type: "string",  example: "John Smith"),
        new OA\Property(property: "email",         type: "string",  format: "email", example: "agent@myagenci.ai"),
        new OA\Property(property: "role",          type: "string",  nullable: true,  example: "agent"),
        new OA\Property(property: "is_active",     type: "boolean", example: true),
        new OA\Property(property: "branch_id",     type: "integer", nullable: true,  example: 2),
        new OA\Property(property: "branch",        type: "object",  nullable: true,
            properties: [
                new OA\Property(property: "id",   type: "integer", example: 2),
                new OA\Property(property: "name", type: "string",  example: "Downtown Office"),
            ]
        ),
        new OA\Property(property: "last_login_at", type: "string",  format: "date-time", nullable: true),
        new OA\Property(property: "profile_photo", type: "string",  format: "uri",       nullable: true),
    ]
)]
#[OA\Schema(schema: "Branch", type: "object",
    properties: [
        new OA\Property(property: "id",   type: "integer", example: 2),
        new OA\Property(property: "name", type: "string",  example: "Downtown Office"),
    ]
)]
#[OA\Schema(schema: "ErrorResponse", type: "object",
    properties: [
        new OA\Property(property: "status",  type: "boolean", example: false),
        new OA\Property(property: "message", type: "string",  example: "An error occurred."),
    ]
)]
#[OA\Schema(schema: "UnauthenticatedResponse", type: "object",
    properties: [
        new OA\Property(property: "message", type: "string", example: "Unauthenticated."),
    ]
)]
#[OA\Schema(schema: "ValidationErrorResponse", type: "object",
    properties: [
        new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
        new OA\Property(property: "errors",  type: "object"),
    ]
)]
class SwaggerSchemas {}
