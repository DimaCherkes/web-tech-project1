<?php

namespace App\Service;

use App\Repository\GameRepository;

class GameService
{
    private GameRepository $gameRepository;

    public function __construct()
    {
        $this->gameRepository = new GameRepository();
    }

    public function getAllYears(): array
    {
        return $this->gameRepository->findAllYears();
    }
}
