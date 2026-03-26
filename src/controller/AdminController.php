<?php

namespace App\Controller;

use App\Service\AdminService;
use App\Core\Logger;

class AdminController
{
    private AdminService $adminService;

    public function __construct()
    {
        $this->adminService = new AdminService();
    }

    private function checkAuth(): void
    {
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header("location: /project1/login");
            exit;
        }
    }

    public function index(): void
    {
        $this->checkAuth();
        $data = $this->adminService->getAllData();
        require __DIR__ . '/../view/admin.php';
    }

    // --- Athlete ---
    public function createAthlete(): void
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminService->createAthlete($_POST);
        }
        header("location: /project1/admin");
    }

    public function updateAthlete(): void
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $this->adminService->updateAthlete($id, $_POST);
        }
        header("location: /project1/admin");
    }

    public function deleteAthlete(): void
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->adminService->deleteAthlete($id);
        }
        header("location: /project1/admin");
    }

    // --- Country ---
    public function createCountry(): void
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminService->createCountry($_POST['name'], $_POST['code'] ?: null);
        }
        header("location: /project1/admin");
    }

    public function updateCountry(): void
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminService->updateCountry((int)$_POST['id'], $_POST['name'], $_POST['code'] ?: null);
        }
        header("location: /project1/admin");
    }

    public function deleteCountry(): void
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->adminService->deleteCountry($id);
        }
        header("location: /project1/admin");
    }

    // --- Discipline ---
    public function createDiscipline(): void
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminService->createDiscipline($_POST['name'], $_POST['category'] ?: null);
        }
        header("location: /project1/admin");
    }

    public function updateDiscipline(): void
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminService->updateDiscipline((int)$_POST['id'], $_POST['name'], $_POST['category'] ?: null);
        }
        header("location: /project1/admin");
    }

    public function deleteDiscipline(): void
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->adminService->deleteDiscipline($id);
        }
        header("location: /project1/admin");
    }

    // --- Olympic Game ---
    public function createGame(): void
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminService->createGame((int)$_POST['year'], $_POST['type'], $_POST['city'], (int)$_POST['countryId']);
        }
        header("location: /project1/admin");
    }

    public function updateGame(): void
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminService->updateGame((int)$_POST['id'], (int)$_POST['year'], $_POST['type'], $_POST['city'], (int)$_POST['countryId']);
        }
        header("location: /project1/admin");
    }

    public function deleteGame(): void
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->adminService->deleteGame($id);
        }
        header("location: /project1/admin");
    }

    // --- Athlete Medal ---
    public function createAthleteMedal(): void
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminService->createAthleteMedal((int)$_POST['athleteId'], (int)$_POST['gameId'], (int)$_POST['disciplineId'], (int)$_POST['medalTypeId']);
        }
        header("location: /project1/admin");
    }

    public function updateAthleteMedal(): void
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminService->updateAthleteMedal((int)$_POST['id'], (int)$_POST['athleteId'], (int)$_POST['gameId'], (int)$_POST['disciplineId'], (int)$_POST['medalTypeId']);
        }
        header("location: /project1/admin");
    }

    public function deleteAthleteMedal(): void
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->adminService->deleteAthleteMedal($id);
        }
        header("location: /project1/admin");
    }
}
