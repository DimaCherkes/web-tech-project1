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

    private function checkAuth(): void
    {
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header("location: /project1/login");
            exit;
        }
    }

    /**
     * GET /api/allOlympicGames
     */
    public function getAll(): void {
        header('Content-Type: application/json; charset=utf-8');

        $queryParams = $_GET;

        try {
            $result = $this->gameService->getAll($queryParams);

            http_response_code(200);
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
            Logger::error("Error in GameController::getAll", $e);

            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * POST /api/createOlympicGame
     */
    public function create(): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');

        $data = $this->getRequestData();

        try {
            $id = $this->gameService->create($data);
            if ($id > 0) {
                http_response_code(201);
                echo json_encode(['id' => $id, 'message' => 'Olympic game created successfully']);
            } else {
                http_response_code(409);
                echo json_encode(['error' => 'Olympic game already exists']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/olympicGame/{id}
     */
    public function getById(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $result = $this->gameService->getById($id);
            if (!$result) {
                http_response_code(404);
                echo json_encode(['error' => 'Olympic game not found']);
                return;
            }

            http_response_code(200);
            echo json_encode(['data' => $result->toArray()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }
    }

    /**
     * PUT /api/olympicGame/{id}
     */
    public function update(int $id): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');

        $data = $this->getRequestData();
        try {
            $success = $this->gameService->update($id, $data);

            if ($success) {
                http_response_code(200);
                echo json_encode(['message' => 'Olympic game updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Olympic game not found or no changes made']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }
    }

    /**
     * DELETE /api/olympicGame/{id}
     */
    public function delete(int $id): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $success = $this->gameService->delete($id);

            if ($success) {
                http_response_code(204);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Olympic game not found']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }
    }

    private function getRequestData(): array
    {
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        if (strpos($contentType, "application/json") !== false) {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            return $data ?: [];
        }
        return $_POST;
    }
}
