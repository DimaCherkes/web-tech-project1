<?php

namespace App\controller;

use App\service\AthleteMedalService;
use App\Service\CountryService;
use App\Core\Logger;

class AthleteMedalController {
    private CountryService $countryService;
    private AthleteMedalService $service;

    public function __construct() {
        $this->countryService = new CountryService();
        $this->service = new AthleteMedalService();
    }

    private function checkAuth(): void
    {
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header("location: /project1/login");
            exit;
        }
    }

    /**
     * GET /api/allAthleteMedals
     */
    public function getAll(): void {
        header('Content-Type: application/json; charset=utf-8');

        $queryParams = $_GET;

        try {
            $result = $this->service->getAll($queryParams);

            http_response_code(200);
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
            Logger::error("Error in AthleteMedalController::getAll", $e);

            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * POST /api/createAthleteMedal
     */
    public function create(): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');

        $data = $this->getRequestData();

        try {
            $id = $this->service->create($data);
            if ($id > 0) {
                http_response_code(201);
                echo json_encode(['id' => $id, 'message' => 'AthleteMedal created successfully']);
            } else {
                http_response_code(409);
                echo json_encode(['error' => 'AthleteMedal already exists']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/athleteMedal/{id}
     */
    public function getById(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $entity = $this->service->getById($id);
            if (!$entity) {
                http_response_code(404);
                echo json_encode(['error' => 'Country not found']);
                return;
            }

            http_response_code(200);
            echo json_encode(['data' => $entity->toArray()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }
    }

    /**
     * PUT /api/athleteMedal/{id}
     */
    public function update(int $id): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');

        $data = $this->getRequestData();
        try {
            $success = $this->service->update($id, $data);

            if ($success) {
                http_response_code(200);
                echo json_encode(['message' => 'AthleteMedal updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'AthleteMedal not found or no changes made']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }
    }

    /**
     * DELETE /api/athleteMedal/{id}
     */
    public function delete(int $id): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $success = $this->service->delete($id);

            if ($success) {
                http_response_code(204);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'AthleteMedal not found']);
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
