<?php

namespace App\Controller;

use App\Service\AthleteService;
use App\Dto\AthleteDTO;
use App\Dto\AthleteListDTO;
use App\Core\Logger;

class AthleteController
{
    private AthleteService $athleteService;

    public function __construct()
    {
        $this->athleteService = new AthleteService();
    }

    /**
     * GET /api/athletes
     */
    public function index(): void
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
}
