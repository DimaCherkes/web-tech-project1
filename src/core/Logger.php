<?php

namespace App\Core;

class Logger {
    /**
     * Записывает информационное сообщение в логи Docker
     */
    public static function info(string $message): void {
        self::log("INFO", $message);
    }

    /**
     * Записывает сообщение об ошибке в логи Docker
     */
    public static function error(string $message, \Throwable $e = null): void {
        $fullMessage = $message;
        if ($e) {
            $fullMessage .= " | Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
        }
        self::log("ERROR", $fullMessage);
    }

    /**
     * Внутренний метод для форматирования и записи
     */
    private static function log(string $level, string $message): void {
        $date = date('Y-m-d H:i:s');
        $formattedMessage = sprintf("[%s] [%s] %s\n", $level, $date, $message);
        
        // В Docker вывод в php://stderr попадает в `docker-compose logs`
        file_put_contents('php://stderr', $formattedMessage);
    }
}
