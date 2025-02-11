<?php

namespace Justi\StudentDah\Services;


class UserService
{
    private $conn;

    public function __construct($connection){
        $this->conn=$connection;
    }


    public function saveUser($email,$password):string{
        $response =array();
        try {

            $query = $this->conn->prepare("SELECT email FROM users WHERE email=?");
            $query->bind_param("s", $email);
            $query->execute();
            $query->store_result();
            if ($query->num_rows > 0) {
                http_response_code(409);
                $response["message"] = "User already Excist";
                return json_encode($response);
            }

            $query = $this->conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $hashPassword=  password_hash($password, PASSWORD_DEFAULT);
            $query->bind_param("ss", $email, $hashPassword);
            $query->execute();

            $response["message"] = "Congratulations you have registered";
            return json_encode($response);
        }catch (\Exception $e){

            http_response_code(500);
            $response["message"] = $e->getMessage();
            return json_encode($response);
        }


    }



}