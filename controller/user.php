<?php

require_once 'routes/user.php';

class UserController {
    private $route;

    public function __construct() {
        $this->route = new UserRouter();
    }

    public function controller($method, $endpoint, $data = null) {
        switch ($method) {
            case 'GET':
                // echo json_encode($endpoint);
                 $this->route->GETROUTE($endpoint, $data);
                break;
            case 'POST':
                $this->route->POSTROUTE($endpoint, $data);
                break;
            case 'PUT':
                $this->route->PUTROUTE($endpoint, $data);
                break;
            case 'DELETE':
                $this->route->DELETEROUTE($endpoint, $data);
                break;
            default:
                // Handle unsupported HTTP methods
                http_response_code(405);
                echo json_encode(array("message" => "Method not allowed"));
                break;
        }
    }
    
}
?>
