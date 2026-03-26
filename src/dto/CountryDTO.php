<?php

namespace App\dto;

class CountryDTO
{
    private $id;
    private $name;
    private $code;

    public function __construct(array $data) {
        $this->id = (int) ($data['id'] ?? 0);
        $this->name = (string) ($data['name'] ?? '');
        $this->code = (string) ($data['code'] ?? '');
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code
        ];
    }
}