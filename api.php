<?php
 require_once 'utility/headers.php';

require_once 'controller/user.php';


$user = new UserController();
$method = $_SERVER['REQUEST_METHOD'];
$headertoken = $_REQUEST['token'] ? $_REQUEST['token'] : null;
$url  = $_SERVER['PATH_INFO'];

// if (isset($_SERVER["HTTP_X_API_KEY"])) {
//     $token = $_SERVER["HTTP_X_API_KEY"];
// }

//$checkValidToken = checkToken($token);
$token = str_replace("=", "", base64_encode(random_bytes(160 / 8)));
header('Content-Type: application/json');
header("Authorization: Basic " . $token);
$data = json_decode(file_get_contents('php://input'), true);


$gepUrl = gep($url);

 pathDir('/users', $gepUrl) ? $user->controller($method, $gepUrl, $data) : err_type(404);
