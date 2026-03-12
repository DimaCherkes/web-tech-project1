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

    public function login(): void
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            header("location: /");
            exit;
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $result = $this->userService->authenticate($email, $password);

            if ($result['success']) {
                $_SESSION['loggedin'] = true;
                $_SESSION['userId'] = $result['user']['id'];
                $_SESSION['fullName'] = $result['user']['fullName'];
                $_SESSION['email'] = $result['user']['email'];

                Logger::info("User logged in: " . $email);
                header("location: /");
                exit;
            } else {
                $errors[] = $result['error'];
            }
        }

        require __DIR__ . '/../view/login.php';
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        header("location: /login");
        exit;
    }
}
