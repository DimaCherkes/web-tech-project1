<?php

namespace App\Dto;

class AthleteListDTO
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public int $year;
    public string $country;
    public string $sportName;

    public function __construct(array $data) {
        $this->id = (int) ($data['id'] ?? 0);
        $this->firstName = $data['firstName'] ?? $data['first_name'] ?? '';
        $this->lastName = $data['lastName'] ?? $data['last_name'] ?? '';
        $this->year = (int) ($data['year'] ?? 1900);
        $this->country = $data['country'] ?? '';
        $this->sportName = $data['sportName'] ?? '';
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'year' => $this->year,
            'country' => $this->country,
            'sportName' => $this->sportName
        ];
    }
}
