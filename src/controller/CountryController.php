<?php

namespace App\Controller;

use App\Dto\AthleteDTO;
use App\Dto\AthleteListDTO;
use App\Service\AthleteService;
use App\Service\CountryService;
use App\Core\Logger;

class CountryController {
    private CountryService $countryService;
    private AthleteService $athleteService;

    public function __construct() {
        $this->countryService = new CountryService();
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
     * GET /api/allCountries
     */
    public function getAll(): void {
        header('Content-Type: application/json; charset=utf-8');

        $queryParams = $_GET;

        try {
            $result = $this->countryService->getAll($queryParams);

            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
            Logger::error("Error in CountryController::getAllCountries", $e);

            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * POST /api/createCountry
     */
    public function create(): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');

        $data = $this->getRequestData();

        try {
            $id = $this->countryService->create($data);
            if ($id > 0) {
                http_response_code(201);
                echo json_encode(['id' => $id, 'message' => 'Country created successfully']);
            } else {
                http_response_code(409);
                echo json_encode(['error' => 'Country already exists']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/country/{id}
     */
    public function getById(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $country = $this->countryService->getById($id);
            if (!$country) {
                http_response_code(404);
                echo json_encode(['error' => 'Country not found']);
                return;
            }

            http_response_code(200);
            echo json_encode(['data' => $country->toArray()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
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
            $success = $this->countryService->update($id, $data);

            if ($success) {
                http_response_code(200);
                echo json_encode(['message' => 'Country updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Country not found or no changes made']);
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
            $success = $this->countryService->delete($id);

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
