<?php
$method = $_SERVER['REQUEST_METHOD'];
$url  = $_SERVER['PATH_INFO'];
$headers = apache_request_headers();
$receivedToken = $headers['http_x_api_key'];
// $token = str_replace("=", "", base64_encode(random_bytes(160 / 8)));

function checkToken($token) {
    
    //authorized tokens
    $tokenList = ["fa3b2c9c-a96d-48a8-82ad-0cb775dd3e5d" => ""];
   
    if (!isset($token)) {
        err_type(505);
    }

   else if (!isset($tokenList[$token])) {
    err_type(509);
    }
    else {
        return true;
    }
};