<?php

namespace App\Controller;

use App\Service\UserService;
use App\Core\Logger;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function register(): void
    {
        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Logger::info("User registration attempt for email: " . ($_POST['email'] ?? 'unknown'));
            
            $data = [
                'firstName' => $_POST['firstName'] ?? '',
                'lastName' => $_POST['lastName'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'password_repeat' => $_POST['password_repeat'] ?? '',
            ];

            $result = $this->userService->register($data);
            
            if ($result['success']) {
                $success = true;
                Logger::info("User registered successfully: " . $data['email']);
            } else {
                $errors = $result['errors'];
            }
        }

        require __DIR__ . '/../view/register.php';
    }
}
