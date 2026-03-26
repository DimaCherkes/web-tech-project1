<?php

namespace App\Controller;

use App\Service\DisciplineService;
use App\Core\Logger;

class DisciplineController
{
    private DisciplineService $disciplineService;

    public function __construct()
    {
        $this->disciplineService = new DisciplineService();
    }

    public function categories(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $categories = $this->disciplineService->getAllCategories();
            echo json_encode(['data' => $categories]);
        } catch (\Throwable $e) {
            Logger::error("Error in DisciplineController", $e);
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
     * GET /api/allDisciplines
     */
    public function getAll(): void {
        header('Content-Type: application/json; charset=utf-8');

        $queryParams = $_GET;

        try {
            $result = $this->disciplineService->getAll($queryParams);

            http_response_code(200);
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
            Logger::error("Error in CountryController::getAllCountries", $e);

            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * POST /api/createDiscipline
     */
    public function create(): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');

        $data = $this->getRequestData();

        try {
            $id = $this->disciplineService->create($data);
            if ($id > 0) {
                http_response_code(201);
                echo json_encode(['id' => $id, 'message' => 'Discipline created successfully']);
            } else {
                http_response_code(409);
                echo json_encode(['error' => 'Discipline already exists']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/discipline/{id}
     */
    public function getById(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $result = $this->disciplineService->getById($id);
            if (!$result) {
                http_response_code(404);
                echo json_encode(['error' => 'Discipline not found']);
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
     * PUT /api/discipline/{id}
     */
    public function update(int $id): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');

        $data = $this->getRequestData();
        try {
            $success = $this->disciplineService->update($id, $data);

            if ($success) {
                http_response_code(200);
                echo json_encode(['message' => 'Discipline updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Discipline not found or no changes made']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }
    }

    /**
     * DELETE /api/discipline/{id}
     */
    public function delete(int $id): void
    {
        $this->checkAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $success = $this->disciplineService->delete($id);

            if ($success) {
                http_response_code(204);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Discipline not found']);
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
