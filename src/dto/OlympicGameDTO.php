<?php

namespace App\dto;

class OlympicGameDTO
{
    private int $id;
    private int $year;
    private string $type;
    private string $city;
    private string $countryName;

    public function __construct(array $data) {
        $this->id = (int) ($data['id'] ?? 0);
        $this->year = (int) ($data['year'] ?? 0);
        $this->type = (string) ($data['type'] ?? '');
        $this->city = (string) ($data['city'] ?? '');
        $this->countryName = (string) ($data['country_name'] ?? '');
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'type' => $this->type,
            'city' => $this->city,
            'countryName' => $this->countryName,
        ];
    }
}