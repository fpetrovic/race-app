<?php

namespace App\Factory;

use App\Entity\Race;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Race>
 *
 * @method        Race|Proxy                       create(array|callable $attributes = [])
 * @method static Race|Proxy                       createOne(array $attributes = [])
 * @method static Race|Proxy                       find(object|array|mixed $criteria)
 * @method static Race|Proxy                       findOrCreate(array $attributes)
 * @method static Race|Proxy                       first(string $sortedField = 'id')
 * @method static Race|Proxy                       last(string $sortedField = 'id')
 * @method static Race|Proxy                       random(array $attributes = [])
 * @method static Race|Proxy                       randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static Race[]|Proxy[]                   all()
 * @method static Race[]|Proxy[]                   createMany(int $number, array|callable $attributes = [])
 * @method static Race[]|Proxy[]                   createSequence(iterable|callable $sequence)
 * @method static Race[]|Proxy[]                   findBy(array $attributes)
 * @method static Race[]|Proxy[]                   randomRange(int $min, int $max, array $attributes = [])
 * @method static Race[]|Proxy[]                   randomSet(int $number, array $attributes = [])
 */
final class RaceFactory extends ModelFactory
{
    protected static int $count = 0;

    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        ++self::$count;

        return [
            'averageFinishTimeForLongDistance' => self::faker()->numberBetween(15000, 24000),
            'averageFinishTimeForMediumDistance' => self::faker()->numberBetween(9000, 18000),
            'raceDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'title' => sprintf('race %s', (self::$count % 15) + 1),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Race $race): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Race::class;
    }
}
