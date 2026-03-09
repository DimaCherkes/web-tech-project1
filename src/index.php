<?php

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
    // Example: App\Controller\AthleteController -> src/controller/AthleteController.php
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

// Simple Router
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Simple path matching
if ($path === '/' || $path === '/index.php') {
    require __DIR__ . '/view/index.php';
    exit;
}

// Serve static files
if (preg_match('/\.(?:css|js)$/', $path)) {
    $file = __DIR__ . $path;
    if (file_exists($file)) {
        $mime = str_ends_with($path, '.css') ? 'text/css' : 'application/javascript';
        header("Content-Type: $mime");
        readfile($file);
        exit;
    }
}

if ($path === '/api/athletes') {
    $controller = new AthleteController();
    $controller->index();
    exit;
}

if ($path === '/api/athletesList') {
    $controller = new AthleteController();
    $controller->athletesList();
    exit;
}

if ($path === '/api/years') {
    $controller = new GameController();
    $controller->years();
    exit;
}

if ($path === '/api/categories') {
    $controller = new DisciplineController();
    $controller->categories();
    exit;
}

// 404 Not Found
http_response_code(404);
echo json_encode(['error' => 'Not Found']);
