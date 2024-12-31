<?php

declare(strict_types=1);

function err_type(int $type, string $customMessage = ''): void
{
    $message = match ($type) {
        400 => $customMessage ?: "Bad Request: Invalid or missing data",
        404 => "Endpoint not found",
        405 => "Method not allowed",
        500 => "Internal Server Error",
        505 => "No Key was received to authorize the operation",
        509 => "Invalid Api Key",
        default => "Unknown error",
    };

    $statusCode = match ($type) {
        400 => 400,
        404 => 404,
        405 => 405,
        500 => 500,
        505, 509 => 403,
        default => 402,
    };

    http_response_code($statusCode);
    echo json_encode(["message" => $message]);
}