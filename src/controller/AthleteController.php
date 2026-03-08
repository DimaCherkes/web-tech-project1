<?php

namespace App\Controller;

use App\Service\AthleteService;
use App\Dto\AthleteDto;

class AthleteController {
    private AthleteService $athleteService;

    public function __construct() {
        $this->athleteService = new AthleteService();
    }

    /**
     * GET /api/athletes
     */
    public function index(): void {
        header('Content-Type: application/json; charset=utf-8');

        $queryParams = $_GET;
        $result = $this->athleteService->getAllAthletes($queryParams);
        
        // Final mapping to simple arrays for json_encode
        $response = [
            'data' => array_map(fn(AthleteDto $dto) => $dto->toArray(), $result['items']),
            'pagination' => $result['pagination']
        ];

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
