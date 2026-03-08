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

// Simple Router
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Simple path matching
if ($path === '/api/athletes') {
    $controller = new AthleteController();
    $controller->index();
    exit;
}

// 404 Not Found
http_response_code(404);
echo json_encode(['error' => 'Not Found']);
