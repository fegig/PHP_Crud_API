<?php
require_once 'model/user.php';

class UserRouter
{
    private $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function PUTROUTE($endpoint, $data)
    {
        if (preg_match('/^\/users\/(\d+)$/', $endpoint, $matches)) {
            $userId = $matches[1];
            $result = $this->model->updateUser($userId, $data);
            echo json_encode(['success' => $result]);
        }
    }

    public function POSTROUTE($endpoint, $data)
    {
        if (!preg_match('/^\/users\/(\d+)$/', $endpoint, $matches)) {
            $result = $this->model->addUser($data);
            echo json_encode(['success' => $result]);
        } else {
            err_type(404);
        }
    }

    public function GETROUTE($endpoint)
    {
        if ($endpoint === '/users') {
            // Get all users
            $users = $this->model->getAllUsers();
            echo json_encode(["data" => $users]);
        } else if (preg_match('/^\/users\/(\d+)$/', $endpoint, $matches)) {

            // Get user by ID 
            $userId = $matches[1];
            $result = $this->model->getAllUsers();

            $found = false;
            foreach ($result as $user) {
                if ($user['id'] == $userId) {
                    echo json_encode(['data' => $user]);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                err_type(404);
            }
        } else {
            // Invalid endpoint
            http_response_code(404);
            echo json_encode(["error" => "Invalid endpoint"]);
        }
    }

    public function DELETEROUTE($endpoint)
    {
        if (preg_match('/^\/users\/(\d+)$/', $endpoint, $matches)) {
            $userId = $matches[1];
            $result = $this->model->deleteUser($userId);
            echo json_encode(['success' => $result]);
        }
    }
}
