<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basic API With PHP - Documentation</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-gray-900 text-white shadow-lg">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <a href="#" class="text-xl font-bold">Basic API With PHP</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-3xl font-bold mb-8">API Documentation</h1>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Setup</h2>
                <h3 class="text-xl font-medium mb-2">Environment Configuration</h3>
                <div class="bg-gray-100 rounded-lg p-4 font-mono text-sm">
                    <pre>DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_username
DB_PASS=your_password
API_KEY=your_api_key

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls</pre>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Local Development</h2>
                <p class="mb-2">Start the PHP development server locally:</p>
                <div class="bg-gray-100 rounded-lg p-4 font-mono text-sm">
                    <pre>php -S localhost:8000</pre>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">API Endpoints</h2>
                <h3 class="text-xl font-medium mb-2">Base URL</h3>
                <div class="bg-gray-100 rounded-lg p-4 font-mono text-sm mb-4">
                    <pre>http://localhost:8000</pre>
                </div>
                <h3 class="text-xl font-medium mb-2">Available Endpoints</h3>
                <ul class="bg-gray-100 rounded-lg divide-y divide-gray-200">
                    <li class="p-4">GET / - Base endpoint</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Requirements</h2>
                <ul class="bg-gray-100 rounded-lg divide-y divide-gray-200">
                    <li class="p-4">PHP 8.1 or higher</li>
                    <li class="p-4">MySQL/MariaDB</li>
                    <li class="p-4">Composer (for dependency management)</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Installation</h2>
                <ol class="bg-gray-100 rounded-lg divide-y divide-gray-200">
                    <li class="p-4">1. Clone the repository</li>
                    <li class="p-4">2. Copy .env.example to .env and configure your environment variables</li>
                    <li class="p-4">
                        3. Install dependencies:
                        <div class="bg-gray-200 rounded-lg p-4 font-mono text-sm mt-2">
                            <pre>composer install</pre>
                        </div>
                    </li>
                </ol>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Security</h2>
                <ul class="bg-gray-100 rounded-lg divide-y divide-gray-200">
                    <li class="p-4">Ensure your .env file is included in .gitignore</li>
                    <li class="p-4">API key authentication is required for all endpoints</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">API Development Guide</h2>
                
                <h3 class="text-xl font-medium mb-4">Router Configuration</h3>
                <p class="mb-2">Routes are defined in <code class="bg-gray-100 px-2 py-1 rounded">routes/api.php</code> using the Router class:</p>
                <div class="bg-gray-100 rounded-lg p-4 font-mono text-sm mb-6">
                    <pre>use Utility\Core\Router;

$router = new Router();

// Define routes
$router->get('endpoint/{parameter}', 'ControllerName@methodName');
$router->post('endpoint', 'ControllerName@methodName');
$router->put('endpoint/{parameter}', 'ControllerName@methodName');
$router->delete('endpoint/{parameter}', 'ControllerName@methodName');

// Handle the request
$router->handleRequest();</pre>
                </div>

                <h3 class="text-xl font-medium mb-4">Creating Controllers</h3>
                <p class="mb-2">1. Create new controller files in <code class="bg-gray-100 px-2 py-1 rounded">app/controllers/</code></p>
                <p class="mb-2">2. Basic controller structure:</p>
                <div class="bg-gray-100 rounded-lg p-4 font-mono text-sm mb-6">
                    <pre><?php echo htmlspecialchars('<?php

declare(strict_types=1);

namespace App\Controllers;

use Utility\Core\Response;
use Utility\Core\Validator;

class YourController {
    private YourModel $model;

    public function __construct() {
        $this->model = new YourModel();
    }

    // GET request handler
    public function getItem(?string $id = null): void {
        try {
            // Handle both single item and collection requests
            $result = $id ? $this->model->getItem($id) : $this->model->getAllItems();
            Response::successResponse($result, 200);
        } catch (Exception $e) {
            Response::errorResponse($e->getMessage(), 500);
        }
    }

    // POST request handler
    public function createItem(): void {
        try {
            // Validate incoming data
            Validator::validate($_POST, [
                "field1" => "required",
                "field2" => "required|email"
            ]);

            $result = $this->model->createItem($_POST);
            Response::successResponse(["message" => "Item created"], 200);
        } catch (Exception $e) {
            Response::errorResponse($e->getMessage(), 400);
        }
    }
}'); ?></pre>
                </div>

                <h3 class="text-xl font-medium mb-4">Validation Rules</h3>
                <p class="mb-2">Available validation rules for use with <code class="bg-gray-100 px-2 py-1 rounded">Validator::validate()</code>:</p>
                <ul class="bg-gray-100 rounded-lg divide-y divide-gray-200 mb-6">
                    <li class="p-4"><code>required</code>: Field must be present and not empty</li>
                    <li class="p-4"><code>email</code>: Must be a valid email format</li>
                    <li class="p-4"><code>string</code>: Must be a string value</li>
                    <li class="p-4">Additional rules can be added in the Validator class</li>
                </ul>

                <h3 class="text-xl font-medium mb-4">Response Format</h3>
                <p class="mb-2">The API returns JSON responses in the following format:</p>
                
                <p class="mt-4 mb-2">Success Response:</p>
                <div class="bg-gray-100 rounded-lg p-4 font-mono text-sm mb-4">
                    <pre>{
    "success": true,
    "data": {
        // Response data
    }
}</pre>
                </div>

                <p class="mt-4 mb-2">Error Response:</p>
                <div class="bg-gray-100 rounded-lg p-4 font-mono text-sm">
                    <pre>{
    "success": false,
    "error": "Error message",
    "code": 400
}</pre>
                </div>
            </section>

        </div>
    </div>
</body>
</html>
