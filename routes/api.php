<?php

use Utility\Core\Router;
use Utility\Security\Authentication;


$router = new Router();

Authentication::validateRequest();

$router->get('estates/{propertyId}', 'EstateController@getEstate');
$router->get('estates', 'EstateController@getEstate');
$router->post('estates', 'EstateController@createEstate');
$router->put('estates/{propertyId}', 'EstateController@updateEstate');
$router->delete('estates/{propertyId}', 'EstateController@deleteEstate');
$router->post('estateImages', 'EstateController@createImage');
$router->delete('estateImages/{imageId}', 'EstateController@deleteImage');


$router->post('images', 'ImageController@uploadImage');
$router->get('images', 'ImageController@getImage');
$router->get('images/{imageId}', 'ImageController@getImage');








$router->handleRequest();
?>