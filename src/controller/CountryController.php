<?php

namespace App\Controller;

use App\Service\CountryService;
use App\Core\Logger;

class CountryController {
    private CountryService $countryService;

    public function __construct() {
        $this->countryService = new CountryService();
    }

    public function getAllCountries(): void {
        header('Content-Type: application/json; charset=utf-8');

        $queryParams = $_GET;

        try {
            $result = $this->countryService->getAllCountries($queryParams);

            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
            Logger::error("Error in CountryController::getAllCountries", $e);

            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }
}
