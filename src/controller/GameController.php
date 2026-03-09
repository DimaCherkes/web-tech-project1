<?php

namespace App\Controller;

use App\Service\GameService;
use App\Core\Logger;

class GameController
{
    private GameService $gameService;

    public function __construct()
    {
        $this->gameService = new GameService();
    }

    public function years(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $years = $this->gameService->getAllYears();
            echo json_encode(['data' => $years]);
        } catch (\Throwable $e) {
            Logger::error("Error in GameController", $e);
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }
    }
}
