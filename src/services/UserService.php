<?php

namespace Justi\StudentDah\Services;

class UserService
{
    private $conn;

    public function __construct($connection){
        $this->conn=$connection;
    }


    public function register($email,$password,$name,$phone,$gender):string{
        $response =array();

        try {

            $query = $this->conn->prepare("SELECT email FROM users WHERE email=?");
            $query->bind_param("s", $email);
            $query->execute();
            $query->store_result();
            if ($query->num_rows > 0) {
                http_response_code(409);
                $response["message"] = "User already Exist";
                return json_encode($response);
            }
            if(!$this->validateGender($gender)){
                http_response_code(409);
                $response["message"] = "GENDER MUST BE MALE,FEMALE OR OTHERS";
                return json_encode($response);
            }

            $query = $this->conn->prepare("INSERT INTO users (email,username,phone,gender, password) VALUES (?,?,?,?,?)");
            $hashPassword=  password_hash($password, PASSWORD_DEFAULT);
            $query->bind_param("sssss", $email,$name,$phone,$gender, $hashPassword);
            $query->execute();
            http_response_code(201);
            $response["message"] = "Congratulations you have registered";
            return json_encode($response);
        }catch (\Exception $e){
            http_response_code(500);
            $response["message"] = $e->getMessage();
            return json_encode($response);
        }


    }

    protected function validateGender($gender){
        $data =["MALE","FEMALE","OTHERS"];
        return in_array($gender,$data)?true:false;

    }


       public function login($email, $password): string
    {
        $response = array();

        try {
            $query = $this->conn->prepare("SELECT email,id,username,gender,phone, password FROM users WHERE email=?");
            $query->bind_param("s", $email);
            $query->execute();
            $result = $query->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $hashPassword = $user['password'];

                if (password_verify($password, $hashPassword)) {
                    $service = new JwtService();
                    $token = $service->generateJwt($email);

                    http_response_code(200);
                    $response["message"] = "Login Successful";
                    $response["token"] = $token;

                    return json_encode($this->mapUserToResponse($user,$response));
                } else {
                    http_response_code(401);
                    $response["message"] = "Invalid email or password";
                    return json_encode($response);
                }
            } else {
                http_response_code(401);
                $response["message"] = "Invalid email or password";
                return json_encode($response);
            }
        } catch (\Exception $e) {
            // Handle any exceptions
            http_response_code(500);
            $response["message"] = $e->getMessage();
            return json_encode($response);
        }
    }

    public function getUserInfo(int $userId):string
    {
        $response = array();

        try {
            $query = $this->conn->prepare("SELECT * FROM users WHERE id=?");
            $query->bind_param("i", $userId);
            $query->execute();
            $result = $query->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                http_response_code(200);
                return json_encode($this->mapUserToResponse($user,$response));
                } else {
                    http_response_code(404);
                    $response["message"] = "User not found";
                    return json_encode($response);
                }

        } catch (\Exception $e) {
            // Handle any exceptions
            http_response_code(500);
            $response["message"] = $e->getMessage();
            return json_encode($response);
        }

    }

    protected function mapUserToResponse(array $user,array $response):array
    {

        $response["userId"] = $user['id'];
        $response["email"] = $user['email'];
        $response["name"] = $user['username'];
        $response["gender"] = $user['gender'];
        $response["phone"] = $user['phone'];
        return $response;

    }









}