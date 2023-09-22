<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

class RaceResultsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly Connection $connection)
    {
        parent::__construct($registry, RaceResultsRepository::class);
    }

    /**
     * @throws Exception
     */
    public function updateOverallPlacements(): void
    {
        $sql = "
            WITH ranked AS (
                SELECT rr.id,
                    dense_rank() OVER (ORDER BY rr.finish_time ASC) as rank
                FROM race_results rr
                WHERE rr.distance = 'long'
            )
            UPDATE race_results rr
            SET overall_placement = r.rank
            FROM ranked r
            WHERE rr.id = r.id;
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->executeStatement();
    }

    /**
     * @throws Exception
     */
    public function updateAgeCategoryPlacements(): void
    {
        $sql = "
            WITH RankedResults AS (
            SELECT
                rr.id,
                rr.distance,
                rr.age_category,
                rr.finish_time,
                CASE
                    WHEN lag(rr.age_category) OVER (PARTITION BY rr.age_category ORDER BY rr.finish_time ASC) = rr.age_category THEN
                        dense_rank() OVER (PARTITION BY rr.age_category ORDER BY rr.finish_time ASC)
                    ELSE
                        1
                   END AS new_age_category_placement
            FROM race_results rr
            WHERE rr.distance = 'long'
        )
        UPDATE race_results rr
        SET age_category_placement = r.new_age_category_placement
        FROM RankedResults r WHERE rr.id = r.id;
";
        $stmt = $this->connection->prepare($sql);
        $stmt->executeStatement();
    }
}
