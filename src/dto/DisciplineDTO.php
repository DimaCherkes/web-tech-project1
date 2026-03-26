<?php

namespace App\dto;

class DisciplineDTO
{
    private int $id;
    private string $name;
    private string $category;

    public function __construct(array $data) {
        $this->id = (int) ($data['id'] ?? 0);
        $this->name = (string) ($data['name'] ?? '');
        $this->category = (string) ($data['category'] ?? '');
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->category
        ];
    }
}