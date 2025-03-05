<?php

use Justi\StudentDah\Middleware\AuthMiddleware;
use Justi\StudentDah\Services\UserService;
use Justi\StudentDah\Utils\Headers;
require_once '../../vendor/autoload.php';

include '../config/Config.php';

$authMiddleware = new AuthMiddleware();
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $authMiddleware->handle($_SERVER);  // Pass the request headers to the middleware

    $headers = new Headers($_SERVER['REQUEST_URI']);
    $userId=$headers->getHeaders();
    $user = new UserService($conn);
    $response= $user->getUserInfo($userId);

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo $response;

}
else{
    $response["message"] = "Method Type Error";

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    http_response_code(404);
    echo json_encode($response);
}
