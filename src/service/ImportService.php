<?php

namespace App\service;

use App\repository\AthleteMedalsRepository;
use App\repository\AthleteRepository;
use App\repository\CountryRepository;
use App\Repository\DisciplineRepository;
use App\Repository\OlympicGamesRepository;

class ImportService
{
    private UtilsService $utilsService;
    private CountryRepository $countryRepository;
    private OlympicGamesRepository $olympicGamesRepository;
    private DisciplineRepository $disciplineRepository;
    private AthleteRepository $athleteRepository;
    private AthleteMedalsRepository $athleteMedalsRepository;

    public function __construct()
    {
        $this->utilsService = new UtilsService();
        $this->countryRepository = new CountryRepository();
        $this->olympicGamesRepository = new OlympicGamesRepository();
        $this->disciplineRepository = new DisciplineRepository();
        $this->athleteRepository = new AthleteRepository();
        $this->athleteMedalsRepository = new AthleteMedalsRepository();
    }

    /**
     * @param string $filePath
     * @return array
     * @throws \Exception
     */
    public function importData(string $filePath): array
    {
        $data = $this->utilsService->parseCsvToAssocArray($filePath, ",");

        if (empty($data)) {
            throw new \Exception("Súbor je prázdny alebo má nesprávny formát.");
        }

        $stats = [
            'total_rows' => count($data),
            'processed' => 0,
            'skipped' => 0
        ];

        // Detect CSV type by headers of the first row
        $firstRow = $data[0];

        if (isset($firstRow['type'], $firstRow['year'], $firstRow['city'], $firstRow['country'])) {
            // Processing games CSV: oh_v2(OH).csv
            foreach ($data as $row) {
                try {
                    $this->countryRepository->insertCountry($row['country'], $row['code'] ?? null);
                    $this->olympicGamesRepository->insertOlympicGames((int)$row['year'], $row['type'], $row['city'], $row['country']);
                    $stats['processed']++;
                } catch (\Exception $e) {
                    // Log error if needed, skip the row
                    $stats['skipped']++;
                }
            }
        } elseif (isset($firstRow['placing'], $firstRow['discipline'], $firstRow['name'], $firstRow['surname'])) {
            // Processing people CSV: oh_v2-people.csv
            foreach ($data as $row) {
                try {
                    // 1. Ensure country exists
                    $this->countryRepository->insertCountry($row['oh_country']);

                    // 2. Ensure game exists
                    $gameId = $this->olympicGamesRepository->insertOlympicGames((int)$row['oh_year'], $row['oh_type'], $row['oh_city'], $row['oh_country']);

                    // 3. Ensure discipline exists
                    $fullDiscipline = trim($row['discipline']);
                    $disciplineCategory = $fullDiscipline; // Default: full string (e.g., 'futbal')

                    if (strpos($fullDiscipline, ' - ') !== false) {
                        $disciplineCategory = trim(explode(' - ', $fullDiscipline)[0]);
                    } elseif (strpos($fullDiscipline, ' ') !== false) {
                        $disciplineCategory = trim(explode(' ', $fullDiscipline)[0]);
                    }

                    $disciplineId = $this->disciplineRepository->insertDiscipline($fullDiscipline, $disciplineCategory);

                    // 4. Ensure athlete exists
                    $athleteId = $this->athleteRepository->insertAthlete(
                        $row['name'],
                        $row['surname'],
                        $this->utilsService->formatDate($row['birth_day']),
                        $row['birth_place'],
                        $row['birth_country'],
                        $this->utilsService->formatDate($row['death_day']),
                        $row['death_place'],
                        $row['death_country']
                    );

                    // 5. Insert placement/result
                    $this->athleteMedalsRepository->insertAthleteMedal($athleteId, $gameId, $disciplineId, $row['placing']);
                    
                    $stats['processed']++;
                } catch (\Exception $e) {
                    // Log error if needed, skip the row
                    $stats['skipped']++;
                }
            }
        } else {
            throw new \Exception("Nerozpoznaný formát CSV hlavičiek.");
        }

        return $stats;
    }
}