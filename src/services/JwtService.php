<?php
namespace Justi\StudentDah\Services;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class JwtService{
    protected $key="secmyfujdjgkl";

    public function generateJwt($email):string{
        $payload = array();
        $payload['iat'] = time();
        $payload['exp'] = time() + 300;
        $payload['email'] = $email;
        $payload['url'] = "http://localhost/student-dah/";
        return JWT::encode($payload,$this->key,'HS256');
    }


    public function verifyToken($jwt): string {
        try {
            // Specify allowed algorithms (e.g., only 'HS256')
            $allowedAlgs = ['HS256'];

            // Decode the JWT
            $decoded = JWT::decode($jwt, $this->key, $allowedAlgs);

            // Return the decoded JWT
            return json_encode(['message' => 'Token is valid', 'data' => $decoded]);
        } catch (Exception $e) {
            // If there's an error (e.g., invalid token or expired token)
            return json_encode(['message' => 'Invalid token: ' . $e->getMessage()]);
        }
    }



}