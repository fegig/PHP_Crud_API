<?php
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = dirname($_SERVER['SCRIPT_NAME']);

if ($base_path !== '/' && strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

$request_uri = trim($request_uri, '/');
