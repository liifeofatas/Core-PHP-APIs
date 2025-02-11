<?php


use Justi\StudentDah\Services\UserService;
require_once '../../vendor/autoload.php';

include '../config/Config.php';


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        $email = $data['email'];
        $password = $data['password'];
        $user = new UserService($conn);
        $response= $user->saveUser($email,$password);
        //gg

        //login
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo $response;





}else{
        $response["message"] = "Method Type Error";

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        http_response_code(404);
        echo json_encode($response);
    }

















