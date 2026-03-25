<?php

namespace App\Service;

use App\Repository\OlympicGamesRepository;

class GameService
{
    private OlympicGamesRepository $gameRepository;

    public function __construct()
    {
        $this->gameRepository = new OlympicGamesRepository();
    }

    public function getAllYears(): array
    {
        return $this->gameRepository->findAllByAllYears();
    }
}
