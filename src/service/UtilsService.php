<?php

namespace App\service;

class UtilsService
{

    public function parseCsvToAssocArray(string $filePath, string $delimiter = ","): array
    {
        $result = [];
        if (!file_exists($filePath)) return [];

        $handle = fopen($filePath, 'r');
        if (!$handle) return [];

        $headers = fgetcsv($handle, 0, $delimiter, "\"", "");
        if (!$headers) {
            fclose($handle);
            return [];
        }

        // Remove BOM (Byte Order Mark) from the first header if present
        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);

        $headers = array_map('trim', $headers);

        // Parsovanie riadkov
        while (($row = fgetcsv($handle, 0, $delimiter, "\"", "")) !== false) {
            if (count($row) === count($headers)) {
                $result[] = array_combine($headers, $row);
            }
        }

        fclose($handle);
        return $result;
    }

    public function formatDate(?string $dateString): ?string {
        if (!$dateString || trim($dateString) === '' || str_contains($dateString, '#')) {
            return null;
        }

        // Format is D/M/YYYY or DD/MM/YYYY
        $parts = explode('/', $dateString);
        if (count($parts) === 3) {
            return sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);
        }

        return null;
    }

}