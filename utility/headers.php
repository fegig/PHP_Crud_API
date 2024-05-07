<?php
require_once 'utility/authentication.php';

function gep($url)
{
    $segments = explode('/', $url);
    if (!(count($segments) > 3)) {
        if (isset($segments[2]) && !empty($segments[2])) {
            return '/' . $segments[1] . '/' . $segments[2];
        } else {
            return '/' . $segments[1];
        }
    } else {
        http_response_code(404);
        return ['error' => "The requested URL is not a valid"];
    }
}

function pathDir($dir, $url)
{

    if (strpos($url, $dir) !== false) {
        return true;
    } else {
        return false;
    }

    // $path = $_SERVER['PHP_SELF'];
    // $path = explode('/', $path);
    // $path = $path[1];
    // return $path;
}


function err_type($type)
{
    switch ($type) {
        case 404:
            http_response_code(404);
            echo json_encode(array("message" => "Endpoint not found"));
            break;
        case 405:
            http_response_code(405);
            echo json_encode(array("message" => "Method not allowed"));
            break;
        case 500:
            http_response_code(500);
            echo json_encode(array("message" => "Internal Server Error"));
            break;
        case 505:
            http_response_code(403);
            echo json_encode(array("message" => "No Key was received to authorize the operation"));
            break;
        case 509:
            http_response_code(403);
            echo json_encode(array("message" => "Invalid Api Key"));
            break;
        default:
            http_response_code(402);
            echo json_encode(array("message" => "Unknown error"));
            break;
    }
    // Handle unsupported endpoints

}
