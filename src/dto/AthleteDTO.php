<?php

namespace App\Dto;

class AthleteDTO {
    public int $id;
    public string $firstName;
    public string $lastName;
    public ?string $birthDate;
    public ?string $birthPlace;
    public ?string $birthCountryName;
    
    // In PHP, we can map arrays manually or use a mapper.
    public function __construct(array $data) {
        $this->id = (int) ($data['id'] ?? 0);
        $this->firstName = $data['first_name'] ?? '';
        $this->lastName = $data['last_name'] ?? '';
        $this->birthDate = $data['birth_date'] ?? null;
        $this->birthPlace = $data['birth_place'] ?? null;
        $this->birthCountryName = $data['birth_country_name'] ?? null;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'birthDate' => $this->birthDate,
            'birthPlace' => $this->birthPlace,
            'birthCountryName' => $this->birthCountryName
        ];
    }
}
