<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function register(array $data): array
    {
        $errors = $this->validateRegistration($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $success = $this->userRepository->create($data);
        
        if (!$success) {
            return ['success' => false, 'errors' => ['General error occurred during registration.']];
        }

        return ['success' => true];
    }

    private function validateRegistration(array $data): array
    {
        $errors = [];

        // Email validation
        if (empty($data['email'])) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } elseif ($this->userRepository->findByEmail($data['email'])) {
            $errors[] = "User with this email already exists.";
        }

        // Names validation
        if (empty($data['firstName'])) {
            $errors[] = "First name is required.";
        } elseif (strlen($data['firstName']) < 2 || strlen($data['firstName']) > 50) {
            $errors[] = "First name must be between 2 and 50 characters.";
        }

        if (empty($data['lastName'])) {
            $errors[] = "Last name is required.";
        } elseif (strlen($data['lastName']) < 2 || strlen($data['lastName']) > 50) {
            $errors[] = "Last name must be between 2 and 50 characters.";
        }

        // Password validation
        if (empty($data['password'])) {
            $errors[] = "Password is required.";
        } elseif (strlen($data['password']) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }

        if ($data['password'] !== ($data['password_repeat'] ?? '')) {
            $errors[] = "Passwords do not match.";
        }

        return $errors;
    }
}
