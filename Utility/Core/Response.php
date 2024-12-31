<?php

declare(strict_types=1);

namespace Utility\Core;

/**
 * Send a JSON response with the given data and status code.
 *
 * @param mixed $data The data to be encoded as JSON.
 * @param int $statusCode The HTTP status code (default: 200).
 * @param array $headers Additional headers to send (default: []).
 * @return void
 */

class Response
{
    public static function jsonResponse($data, int $statusCode = 200, array $headers = []): void
    {
        // Set the content type to JSON
        header('Content-Type: application/json');

        // Set the HTTP status code
        http_response_code($statusCode);

        // Set any additional headers
        foreach ($headers as $name => $value) {
            header("$name: $value");
        }

        // Encode the data as JSON
        $jsonData = json_encode($data);

        // Check for JSON encoding errors
        if ($jsonData === false) {
            // Log the error (you may want to use a proper logging mechanism)
            error_log('JSON encoding failed: ' . json_last_error_msg());

            // Send a 500 Internal Server Error response
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
            exit;
        }

        // Output the JSON data
        echo $jsonData;
        exit;
    }

    /**
     * Send a successful JSON response.
     *
     * @param mixed $data The data to be included in the response.
     * @param int $statusCode The HTTP status code (default: 200).
     * @return void
     */
    public static function successResponse($data, int $statusCode = 200): void
    {
        self::jsonResponse(['success' => true, 'data' => $data], $statusCode);
    }

    /**
     * Send an error JSON response.
     *
     * @param string $message The error message.
     * @param int $statusCode The HTTP status code (default: 400).
     * @return void
     */
    public static function errorResponse(string $message, int $statusCode = 400): void
    {
        self::jsonResponse(['success' => false, 'error' => $message], $statusCode);
    }
}
