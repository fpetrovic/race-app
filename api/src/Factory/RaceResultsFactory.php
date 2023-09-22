<?php

namespace App\Factory;

use App\Entity\DistanceEnum;
use App\Entity\RaceResults;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<RaceResults>
 *
 * @method        RaceResults|Proxy                create(array|callable $attributes = [])
 * @method static RaceResults|Proxy                createOne(array $attributes = [])
 * @method static RaceResults|Proxy                find(object|array|mixed $criteria)
 * @method static RaceResults|Proxy                findOrCreate(array $attributes)
 * @method static RaceResults|Proxy                first(string $sortedField = 'id')
 * @method static RaceResults|Proxy                last(string $sortedField = 'id')
 * @method static RaceResults|Proxy                random(array $attributes = [])
 * @method static RaceResults|Proxy                randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static RaceResults[]|Proxy[]            all()
 * @method static RaceResults[]|Proxy[]            createMany(int $number, array|callable $attributes = [])
 * @method static RaceResults[]|Proxy[]            createSequence(iterable|callable $sequence)
 * @method static RaceResults[]|Proxy[]            findBy(array $attributes)
 * @method static RaceResults[]|Proxy[]            randomRange(int $min, int $max, array $attributes = [])
 * @method static RaceResults[]|Proxy[]            randomSet(int $number, array $attributes = [])
 */
final class RaceResultsFactory extends ModelFactory
{
    protected static int $count = 0;

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        ++self::$count;

        $ageCategories = ['M18-25', 'M26-35', 'M35-44', 'F18-25', 'F26-35', 'F35-44'];
        $distances = [DistanceEnum::MEDIUM, DistanceEnum::LONG];

        return [
            'ageCategory' => $ageCategories[rand(0, 5)],
            'distance' => $distances[rand(0, 1)],
            'finishTime' => self::faker()->numberBetween(9000, 24000),
            'racerFullName' => self::faker()->userName(),
            'race' => RaceFactory::random(),
            'overallPlacement' => (self::$count % 15) + 1,
            'ageCategoryPlacement' => (self::$count % 15) + 1,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(RaceResults $raceResults): void {})
        ;
    }

    protected static function getClass(): string
    {
        return RaceResults::class;
    }
}
