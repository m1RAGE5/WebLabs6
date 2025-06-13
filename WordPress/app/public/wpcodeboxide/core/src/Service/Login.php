<?php

namespace Unicorn\Service;

use Unicorn\Service\JWT\JWT;
use Unicorn\Service\JWT\Key;

class Login
{
    public function login($username, $password)
    {
        $users = require __DIR__ . '/../../credentials.php';

        if (!isset($users['key'])) {
            throw new \Exception('Internal login error.');
        }

        if($users['user'] !== $username) {
            throw new \Exception('Invalid credentials.');
        }

        if (isset($users['password'])) {
            if (password_verify($password, $users['password'])) {

                $key = $users['key'];
                $payload = [
                    'iss' => 'WPCodeBox IDE',
                    'iat' => time(),
                    'data' => [
                        'username' => $username
                    ]
                ];

                return JWT::encode($payload, $key, 'HS256');
            } else {
                throw new \Exception('Invalid credentials.');
            }
        }

        throw new \Exception('Invalid credentials.');
    }

    public function checkToken()
    {

        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_LOWER);

        if(!isset($headers['x-wpcbide-auth'])) {
            return false;
        }

        $token = $headers['x-wpcbide-auth'];
        $token = str_replace('Bearer ', '', $token);

        $users = require __DIR__ . '/../../credentials.php';

        if (!isset($users['key'])) {
            return false;
        }

        $key = $users['key'];

        try {
            JWT::decode($token, new Key($key, 'HS256'));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}