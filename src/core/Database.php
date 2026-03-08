<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $configPath = __DIR__ . '/../../config.php';
            
            if (!file_exists($configPath)) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Config file not found', 'path' => $configPath]);
                exit;
            }

            // Объявляем переменные глобальными ДО подключения файла,
            // чтобы require заполнил именно глобальные переменные.
            global $hostname, $database, $username, $password;
            require $configPath; 
            
            // Если все еще пусто, пробуем еще один способ (некоторые конфиги возвращают массив)
            if (empty($hostname)) {
                // Если файл просто определяет переменные без global, они могут быть в локальной области
                // Но мы используем require выше, так что они должны быть доступны.
            }

            try {
                $dsn = "mysql:host=$hostname;dbname=$database;charset=utf8mb4";
                self::$instance = new PDO($dsn, $username, $password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => 'Database connection failed',
                    'dsn' => "mysql:host=$hostname;dbname=$database",
                    'details' => $e->getMessage()
                ]);
                exit;
            }
        }
        return self::$instance;
    }
}
