<?php
require_once 'utility/headers.php';
require_once 'controller/user.php';


$user = new UserController();


$checkValidToken = checkToken($receivedToken);
if ($checkValidToken) {
    header('Content-Type: application/json');
    header("Cache-Control: no-cache, must-revalidate");
    header("HTTP_X_API_KEY:" . $receivedToken);
    $data = json_decode(file_get_contents('php://input'), true);
    $gepUrl = gep($url);


    
    pathDir('/users', $gepUrl) ? $user->controller($method, $gepUrl, $data) : err_type(404);
}
