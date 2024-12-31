# Basic API With PHP

## Setup

### Environment Configuration
Create a `.env` file in the root directory with the following configuration:
```env
DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_username
DB_PASS=your_password
API_KEY=your_api_key

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
```

### Local Development
Start the PHP development server locally:
```bash
php -S localhost:8000
```

## API Documentation

### Base URL
```
http://localhost:8000
```

### Available Endpoints
- `GET /` - Base endpoint

## Requirements
- PHP 8.1 or higher
- MySQL/MariaDB
- Composer (for dependency management)

## Installation
1. Clone the repository
2. Copy `.env.example` to `.env` and configure your environment variables
3. Install dependencies:
```bash
composer install
```

## Security
- Ensure your `.env` file is included in `.gitignore`
- API key authentication is required for all endpoints

## API Development Guide

### Router Configuration
Routes are defined in `routes/api.php` using the Router class:
```php
use Utility\Core\Router;

$router = new Router();

// Define routes
$router->get('endpoint/{parameter}', 'ControllerName@methodName');
$router->post('endpoint', 'ControllerName@methodName');
$router->put('endpoint/{parameter}', 'ControllerName@methodName');
$router->delete('endpoint/{parameter}', 'ControllerName@methodName');

// Handle the request
$router->handleRequest();
```

### Creating Controllers
1. Create new controller files in `app/controllers/`
2. Basic controller structure:
```php
<?php

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
                'field1' => 'required',
                'field2' => 'required|email'
            ]);

            $result = $this->model->createItem($_POST);
            Response::successResponse(['message' => 'Item created'], 200);
        } catch (Exception $e) {
            Response::errorResponse($e->getMessage(), 400);
        }
    }
}
```

### Validation Rules
Available validation rules for use with `Validator::validate()`:
- `required`: Field must be present and not empty
- `email`: Must be a valid email format
- `string`: Must be a string value
- Additional rules can be added in the Validator class

### Response Format
The API returns JSON responses in the following format:

Success Response:
```json
{
    "success": true,
    "data": {
        // Response data
    }
}
```

Error Response:
```json
{
    "success": false,
    "error": "Error message",
    "code": 400
}
```
