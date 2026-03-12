<?php

namespace App\Service;

use App\Repository\UserRepository;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
use Google\Client;
use Google\Service\Oauth2;

class UserService
{
    private UserRepository $userRepository;
    private TwoFactorAuth $tfa;
    private Client $googleClient;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->tfa = new TwoFactorAuth(new BaconQrCodeProvider(4, '#ffffff', '#000000', 'svg'));
        
        // Setup Google Client
        $this->googleClient = new Client();
        $this->googleClient->setAuthConfig(__DIR__ . '/../../../client_secret_webte.json');
        $this->googleClient->setRedirectUri($this->getRedirectUri());
        $this->googleClient->addScope(["email", "profile"]);
        $this->googleClient->setAccessType("offline");
        $this->googleClient->setIncludeGrantedScopes(true);
    }

    private function getRedirectUri(): string
    {
        $host = $_SERVER['HTTP_HOST'];
        return "https://$host/project1/oauth2callback.php";
    }

    public function getGoogleAuthUrl(): string
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION['google_state'] = $state;
        $this->googleClient->setState($state);
        return $this->googleClient->createAuthUrl();
    }

    public function authenticateGoogle(string $code, string $state): array
    {
        if (!isset($_SESSION['google_state']) || $state !== $_SESSION['google_state']) {
            return ['success' => false, 'error' => 'State mismatch. CSRF protection failed.'];
        }

        $token = $this->googleClient->fetchAccessTokenWithAuthCode($code);
        if (isset($token['error'])) {
            return ['success' => false, 'error' => $token['error_description']];
        }

        $this->googleClient->setAccessToken($token);
        $oauth = new Oauth2($this->googleClient);
        $userInfo = $oauth->userinfo->get();

        $userId = $this->userRepository->syncGoogleUser([
            'google_id' => $userInfo->id,
            'email' => $userInfo->email,
            'firstName' => $userInfo->givenName ?? $userInfo->name ?? 'Google User',
            'lastName' => $userInfo->familyName ?? ''
        ]);

        return [
            'success' => true,
            'user' => [
                'id' => $userId,
                'fullName' => $userInfo->name,
                'email' => $userInfo->email,
                'gid' => $userInfo->id
            ]
        ];
    }

    public function register(array $data): array
    {
        $errors = $this->validateRegistration($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Generate 2FA Secret
        $tfaSecret = $this->tfa->createSecret();
        $data['tfaSecret'] = $tfaSecret;

        $success = $this->userRepository->create($data);
        
        if (!$success) {
            return ['success' => false, 'errors' => ['General error occurred during registration.']];
        }

        // Generate QR Code for the view
        $qrCode = $this->tfa->getQRCodeImageAsDataUri('Olympic Games APP', $tfaSecret);

        return [
            'success' => true, 
            'tfaSecret' => $tfaSecret, 
            'qrCode' => $qrCode
        ];
    }

    public function authenticate(string $email, string $password, ?string $tfaCode = null): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Incorrect email or password.'];
        }

        // Check if 2FA is required (user has a secret)
        if (!empty($user['tfa_secret'])) {
            if ($tfaCode === null) {
                return ['success' => true, 'requires2FA' => true];
            }
            
            if (!$this->tfa->verifyCode($user['tfa_secret'], $tfaCode)) {
                return ['success' => false, 'error' => 'Invalid 2FA code.'];
            }
        }

        return [
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'fullName' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email']
            ]
        ];
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
