<?php

session_start();

require_once __DIR__ . '/vendor/autoload.php';

// Basic Autoloader (Manual since we are not using Composer)
spl_autoload_register(function ($class) {
    // Map App\ namespace to src/ directory
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Since our folders are lowercase in the file system but CamelCase in the namespace
    // we need to handle mapping correctly. 
    $parts = explode('\\', $relative_class);
    $fileName = array_pop($parts);
    $folderPath = strtolower(implode('/', $parts));
    $file = $base_dir . ($folderPath ? $folderPath . '/' : '') . $fileName . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Controller\AthleteController;
use App\Controller\GameController;
use App\Controller\DisciplineController;
use App\Controller\UserController;

// Simple Router
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /project1/index.php
$basePath = dirname($scriptName);      // e.g., /project1

$path = parse_url($requestUri, PHP_URL_PATH);

// Remove base path from the URL path for routing
if ($basePath !== '/' && $basePath !== '\\' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Normalize path: ensure it starts with / and has no trailing slash (except for root)
$path = '/' . ltrim($path, '/');
if ($path !== '/') {
    $path = rtrim($path, '/');
}

// Simple path matching
if ($path === '/' || $path === '/index.php') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        exit;
    }
    require __DIR__ . '/view/index.php';
    exit;
}

if ($path === '/register') {
    $controller = new UserController();
    $controller->register();
    exit;
}

if ($path === '/login') {
    $controller = new UserController();
    $controller->login();
    exit;
}

if ($path === '/history') {
    $controller = new UserController();
    $controller->history();
    exit;
}

if ($path === '/logout') {
    $controller = new UserController();
    $controller->logout();
    exit;
}

if ($path === '/auth/google') {
    $controller = new UserController();
    $controller->googleLogin();
    exit;
}

if ($path === '/oauth2callback.php' || $path === '/google-callback') {
    $controller = new UserController();
    $controller->googleCallback();
    exit;
}

if ($path === '/athlete' || $path === '/athlete.php') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        exit;
    }
    require __DIR__ . '/view/athlete.php';
    exit;
}

// Serve static files
if (preg_match('/\.(?:css|js)$/', $path)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        exit;
    }
    $file = __DIR__ . $path;
    if (file_exists($file)) {
        $mime = str_ends_with($path, '.css') ? 'text/css' : 'application/javascript';
        header("Content-Type: $mime");
        readfile($file);
        exit;
    }
}

// API Routes
if ($path === '/api/athletes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new AthleteController();
    $controller->index();
    exit;
}

if ($path === '/api/athletesList' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new AthleteController();
    $controller->athletesList();
    exit;
}

if ($path === '/api/athlete' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new AthleteController();
    $controller->getAthlete();
    exit;
}

if ($path === '/api/years' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new GameController();
    $controller->years();
    exit;
}

if ($path === '/api/categories' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new DisciplineController();
    $controller->categories();
    exit;
}

// 404 Not Found
http_response_code(404);
echo json_encode(['error' => 'Not Found']);
