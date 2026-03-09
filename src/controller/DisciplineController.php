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
}
