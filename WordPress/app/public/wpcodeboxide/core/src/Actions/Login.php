<?php
namespace Unicorn\Actions;

use Unicorn\Service\Login as LoginService;


class Login
{
    public function execute()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'];
        $password = $data['password'];

        $loginService = new LoginService();
        try {
            $token = $loginService->login($username, $password);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
            die;
        }

        echo json_encode(['token' => $token]);
        die;
    }

}