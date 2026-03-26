<?php

namespace App\Controller;

use App\Service\AthleteService;
use App\Dto\AthleteDTO;
use App\Dto\AthleteListDTO;
use App\Dto\AthleteDetailDTO;
use App\Core\Logger;

class AthleteController
{
    private AthleteService $athleteService;

    public function __construct()
    {
        $this->athleteService = new AthleteService();
    }

    private function checkAuth(): void
    {
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header("location: /project1/login");
            exit;
        }
    }

    /**
     * GET /api/allAthletes
     */
    public function getAll(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $queryParams = $_GET;

        // Log request in Docker logs
        Logger::info("Request: GET /api/athletes with params: " . json_encode($queryParams));

        try {
            $result = $this->athleteService->getAllAthletes($queryParams);

            $response = [
                'data' => array_map(fn(AthleteDTO $dto) => $dto->toArray(), $result['items']),
                'pagination' => $result['pagination']
            ];

            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            Logger::info("Response: " . count($result['items']) . " athletes returned");
        } catch (\Throwable $e) {
            Logger::error("Error in AthleteController", $e);

            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/athletesList
     */
    public function athletesList(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $queryParams = $_GET;

        // Log request
        Logger::info("Request: GET /api/athletesList with params: " . json_encode($queryParams));

        try {
            $result = $this->athleteService->getAthletesList($queryParams);

            $response = [
                'data' => array_map(fn(AthleteListDTO $dto) => $dto->toArray(), $result['items']),
                'pagination' => $result['pagination']
            ];

            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            Logger::info("Response: " . count($result['items']) . " athletes returned in list");
        } catch (\Throwable $e) {
            Logger::error("Error in AthleteController::athletesList", $e);

            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * POST /api/athletes
     */
    public function createAthlete(): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');
        
        $data = $this->getRequestData();
        
        try {
            $id = $this->athleteService->createAthlete($data);
            if ($id > 0) {
                http_response_code(201);
                echo json_encode(['id' => $id, 'message' => 'Athlete created successfully']);
            } else {
                http_response_code(409);
                echo json_encode(['error' => 'Athlete already exists']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/athletes/{id}
     */
    public function getAthleteDetails(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        Logger::info("Request: GET /api/athletes/{" . $id . "}");

        try {
            $athlete = $this->athleteService->getAthleteById($id);
            if (!$athlete) {
                Logger::info("Athlete with ID " . $id . " not found in DB.");
                http_response_code(404);
                echo json_encode(['error' => 'Athlete not found', 'id' => $id]);
                return;
            }

            echo json_encode(['data' => $athlete->toArray()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            Logger::error("Error in AthleteController", $e);
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }
    }

    /**
     * PUT /api/athletes/{id}
     */
    public function update(int $id): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');
        
        $data = $this->getRequestData();

        try {
            $success = $this->athleteService->updateAthlete($id, $data);
            if ($success) {
                http_response_code(200);
                echo json_encode(['message' => 'Athlete updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Athlete not found or no changes made']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }
    }

    /**
     * DELETE /api/athletes/{id}
     */
    public function delete(int $id): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $success = $this->athleteService->deleteAthlete($id);
            if ($success) {
                http_response_code(204);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Athlete not found']);
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
