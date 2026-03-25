<?php

namespace App\Controller;

use App\Core\Logger;
use App\Service\ImportService;

class ImportController
{
    private ImportService $importService;

    public function __construct()
    {
        $this->importService = new ImportService();
    }

    public function import(): void
    {
        // Restriction: Only logged in users can access this page
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header("location: /project1/login");
            exit;
        }

        $errors = [];
        $successMessage = '';
        $importStats = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
            $file = $_FILES['csv_file'];

            // Validate file upload
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Chyba pri nahrávaní súboru.";
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) !== 'csv') {
                    $errors[] = "Povolené sú iba CSV súbory.";
                } else {
                    try {
                        // Pass file to service
                        $importStats = $this->importService->importData($file['tmp_name']);
                        $successMessage = "Súbor bol úspešne spracovaný.";
                        Logger::info("User " . $_SESSION['email'] . " imported CSV file.");
                    } catch (\Exception $e) {
                        $errors[] = "Chyba pri spracovaní: " . $e->getMessage();
                        Logger::error("CSV import failed: " . $e->getMessage());
                    }
                }
            }
        }

        require __DIR__ . '/../view/import.php';
    }
}