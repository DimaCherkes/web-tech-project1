<?php

namespace App\Service;

use App\Repository\DisciplineRepository;

class DisciplineService
{
    private DisciplineRepository $disciplineRepository;

    public function __construct()
    {
        $this->disciplineRepository = new DisciplineRepository();
    }

    public function getAllCategories(): array
    {
        return $this->disciplineRepository->findAllCategories();
    }
}
