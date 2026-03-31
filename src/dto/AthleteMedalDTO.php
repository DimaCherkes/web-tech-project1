<?php

namespace App\dto;

class AthleteMedalDTO
{
    private int $id;
    private int $athleteId;
    private int $gameId;
    private int $disciplineId;
    private int $medalId;

    public function __construct(array $data) {
        $this->id = (int) ($data['id'] ?? -1);
        $this->athleteId = (int) ($data['athlete_id'] ?? -1);
        $this->gameId = (int) ($data['olympic_games_id'] ?? -1);
        $this->disciplineId = (int) ($data['discipline_id'] ?? -1);
        $this->medalId = (int) ($data['medal_type_id'] ?? -1);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'athleteId' => $this->athleteId,
            'gameId' => $this->gameId,
            'disciplineId' => $this->disciplineId,
            'medalId' => $this->medalId
        ];
    }
}