<?php

declare(strict_types=1);

namespace App\Service;

use App\ApiResource\RaceImport;
use App\Entity\Race;
use App\Repository\RaceResultsRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class RaceImportService
{
    public const BATCH_SIZE = 10000;

    public Connection $connection;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RaceResultsRepository $raceResultsRepository,
    )
    {
        $this->connection = $this->em->getConnection();
        $this->connection->getConfiguration()->setSQLLogger(null);
    }

    /**
     * @throws Exception
     */
    public function import(RaceImport $raceImport): void
    {
        $distances = ['medium', 'long'];

        $race = new Race();
        $race->setTitle($raceImport->getTitle());
        $race->setRaceDate($raceImport->getRaceDate());
        $this->em->persist($race);
        $this->em->flush();

        $raceId = $race->getId();

        $avgDistanceTypeFinishTime = ['medium' => ['counter' => 0, 'total' => 0], 'long' => ['counter' => 0, 'total' => 0]];
        $valuesForInsert = [];

        $sql_initial_line = 'INSERT INTO race_results (id, race_id, racer_full_name, distance, finish_time, age_category) VALUES ';
        $sql = '';
        $batchLeftoverToBeExecuted = false;

        foreach ($this->yieldValuesForInsert($raceImport->getFile()->getPathname()) as $lineCount => $valuesForInsertArray) {
            if (!in_array($valuesForInsertArray['distance'], $distances)) {
                throw new Exception();
            }

            ++$avgDistanceTypeFinishTime[$valuesForInsertArray['distance']]['counter'];
            $avgDistanceTypeFinishTime[$valuesForInsertArray['distance']]['total'] += $valuesForInsertArray['finishTime'];

            $batchLeftoverToBeExecuted = true;
            if (0 === $lineCount) {
                $sql .= $sql_initial_line;
            }
            $valuesForInsert[] = sprintf(
                "('%s', '%s', %s, %s, %s, %s)",
                Uuid::v4(),
                $raceId,
                $this->connection->quote($valuesForInsertArray['fullName']),
                $this->connection->quote($valuesForInsertArray['distance']),
                $this->connection->quote($valuesForInsertArray['finishTime']),
                $this->connection->quote($valuesForInsertArray['ageCategory'])
            );

            if (($lineCount + 1) % self::BATCH_SIZE === 0) {
                $sql .= implode(',', $valuesForInsert);
                $this->connection->executeQuery($sql);
                $sql = $sql_initial_line;
                $batchLeftoverToBeExecuted = false;
                $valuesForInsert = [];
            }
        }

        if ($batchLeftoverToBeExecuted) {
            $sql .= implode(',', $valuesForInsert);
            $this->connection->executeQuery($sql);
        }
        $avgMediumDistanceFinishTime = round($avgDistanceTypeFinishTime['medium']['total'] / $avgDistanceTypeFinishTime['medium']['counter']);
        $avgLongDistanceFinishTime = round($avgDistanceTypeFinishTime['long']['total'] / $avgDistanceTypeFinishTime['long']['counter']);
        $race->setAverageFinishTimeForMediumDistance((int) $avgMediumDistanceFinishTime);
        $race->setAverageFinishTimeForLongDistance((int) $avgLongDistanceFinishTime);

        $this->em->persist($race);
        $this->em->flush();

        $this->raceResultsRepository->updateAgeCategoryPlacements();
        $this->raceResultsRepository->updateOverallPlacements();
    }

    protected function yieldValuesForInsert(string $filePath): \Generator
    {
        $handle = fopen($filePath, 'r');

        fgetcsv($handle); // skip csv headers
        while ($lineAsArray = fgetcsv($handle)) {
            if (empty($lineAsArray)) {
                return;
            }

            $fullName = $lineAsArray[0];
            $distance = $lineAsArray[1];
            $finishTime = $lineAsArray[2];
            $ageCategory = $lineAsArray[3];

            $totalTimeInSeconds = FormatIntervalService::formatIntervalFromStringToSeconds($finishTime);

            yield ['fullName' => $fullName, 'distance' => $distance, 'finishTime' => $totalTimeInSeconds, 'ageCategory' => $ageCategory];
        }

        fclose($handle);
    }
}
