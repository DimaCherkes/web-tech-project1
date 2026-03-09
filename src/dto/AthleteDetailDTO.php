<?php

namespace App\Dto;

class AthleteDetailDTO
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public ?string $birthDate;
    public ?string $birthPlace;
    public ?string $birthCountryName;
    public ?string $deathDate;
    public ?string $deathPlace;
    public ?string $deathCountryName;
    public array $participations;

    public function __construct(array $data, array $participations = [])
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->firstName = $data['first_name'] ?? '';
        $this->lastName = $data['last_name'] ?? '';
        $this->birthDate = $data['birth_date'] ?? null;
        $this->birthPlace = $data['birth_place'] ?? null;
        $this->birthCountryName = $data['birth_country_name'] ?? null;
        $this->deathDate = $data['death_date'] ?? null;
        $this->deathPlace = $data['death_place'] ?? null;
        $this->deathCountryName = $data['death_country_name'] ?? null;
        $this->participations = $participations;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'birthDate' => $this->birthDate,
            'birthPlace' => $this->birthPlace,
            'birthCountryName' => $this->birthCountryName,
            'deathDate' => $this->deathDate,
            'deathPlace' => $this->deathPlace,
            'deathCountryName' => $this->deathCountryName,
            'participations' => $this->participations
        ];
    }
}
