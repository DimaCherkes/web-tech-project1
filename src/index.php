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
use App\Controller\ImportController;
use App\Controller\UserController;
use App\Controller\AdminController;
use App\Controller\CountryController;

// Simple REST Router
$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = dirname($scriptName);

$path = parse_url($requestUri, PHP_URL_PATH);
if ($basePath !== '/' && $basePath !== '\\' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$path = '/' . ltrim($path, '/');
if ($path !== '/') $path = rtrim($path, '/');

// Helper for dynamic routes like /api/athletes/123
function matchRoute($pattern, $path) {
    $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $pattern);
    $pattern = "#^" . $pattern . "$#";
    if (preg_match($pattern, $path, $matches)) {
        array_shift($matches);
        return $matches;
    }
    return false;
}

// --- PUBLIC VIEW ROUTES ---
if ($path === '/' || $path === '/index.php') {
    require __DIR__ . '/view/index.php';
    exit;
}

if ($path === '/athlete') {
    require __DIR__ . '/view/athlete.php';
    exit;
}

// --- AUTH ROUTES ---
if ($path === '/login') {
    $controller = new UserController();
    $controller->login();
    exit;
}
if ($path === '/register') {
    $controller = new UserController();
    $controller->register();
    exit;
}
if ($path === '/logout') {
    $controller = new UserController();
    $controller->logout();
    exit;
}
if ($path === '/profile') {
    $controller = new UserController();
    $controller->profile();
    exit;
}
if ($path === '/history') {
    $controller = new UserController();
    $controller->history();
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

// --- API ROUTES ---

// ATHLETES
if ($path === '/api/allAthletes') {
    $controller = new AthleteController();
    if ($method === 'GET') $controller->getAll();            // OK
    exit;
}

if ($path === '/api/createAthlete') {
    $controller = new AthleteController();
    if ($method === 'POST') $controller->createAthlete();          // OK; method with auth
    exit;
}

if ($idMatches = matchRoute('/api/athletes/{id}', $path)) {
    $controller = new AthleteController();
    $id = (int)$idMatches[0];
    if ($method === 'GET') $controller->getAthleteDetails($id);         // OK
    if ($method === 'PUT') $controller->update($id);                    // OK; method with auth
    if ($method === 'DELETE') $controller->delete($id);                 // OK; method with auth
    exit;
}

// COUNTRIES
if ($path === '/api/allCountries') {
    $controller = new CountryController();
    if ($method === 'GET') $controller->getAll();           // OK
    exit;
}

if ($path === '/api/createCountry') {
    $controller = new CountryController();
    if ($method === 'POST') $controller->create();          // OK; method with auth
    exit;
}

if ($idMatches = matchRoute('/api/countries/{id}', $path)) {
    $controller = new CountryController();
    $id = (int)$idMatches[0];
    if ($method === 'GET') $controller->getById($id);           // ?
    if ($method === 'PUT') $controller->update($id);            // ?
    if ($method === 'DELETE') $controller->delete($id);         // ?
    exit;
}

// DISCIPLINES
if ($path === '/api/disciplines') {
    $controller = new AdminController();
    if ($method === 'POST') $controller->createDiscipline();
    exit;
}
if ($idMatches = matchRoute('/api/disciplines/{id}', $path)) {
    $controller = new AdminController();
    $id = (int)$idMatches[0];
    if ($method === 'PUT') $controller->updateDiscipline($id);
    if ($method === 'DELETE') $controller->deleteDiscipline($id);
    exit;
}

// OLYMPIC GAMES
if ($path === '/api/games') {
    $controller = new AdminController();
    if ($method === 'POST') $controller->createGame();
    exit;
}
if ($idMatches = matchRoute('/api/games/{id}', $path)) {
    $controller = new AdminController();
    $id = (int)$idMatches[0];
    if ($method === 'PUT') $controller->updateGame($id);
    if ($method === 'DELETE') $controller->deleteGame($id);
    exit;
}

// MEDALS
if ($path === '/api/medals') {
    $controller = new AdminController();
    if ($method === 'POST') $controller->createAthleteMedal();
    exit;
}
if ($idMatches = matchRoute('/api/medals/{id}', $path)) {
    $controller = new AdminController();
    $id = (int)$idMatches[0];
    if ($method === 'PUT') $controller->updateAthleteMedal($id);
    if ($method === 'DELETE') $controller->deleteAthleteMedal($id);
    exit;
}

// MISC API
if ($path === '/api/years' && $method === 'GET') {
    (new GameController())->years();
    exit;
}
if ($path === '/api/categories' && $method === 'GET') {
    (new DisciplineController())->categories();
    exit;
}

// ADMIN / IMPORT (View)
if ($path === '/admin') {
    $controller = new AdminController();
    $controller->index();
    exit;
}
if ($path === '/admin/athlete/edit') {
    $controller = new AdminController();
    $controller->editAthlete();
    exit;
}
if ($path === '/import') {
    $controller = new ImportController();
    $controller->import();
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

// 404 Not Found
http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'Not Found', 'path' => $path, 'method' => $method]);

