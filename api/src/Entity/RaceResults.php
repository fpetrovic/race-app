<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use App\Service\FormatIntervalService;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[Entity]
#[ApiResource(
    uriTemplate: '/races/{raceId}/race-results',
    operations: [new GetCollection(normalizationContext: ['raceResults:read'])],
    uriVariables: [
        'raceId' => new Link(toProperty: 'race', fromClass: Race::class),
    ]
)]
#[ApiResource(
    operations: [new Patch()],
    denormalizationContext: ['groups' => 'raceResults:write']
)]
#[ApiFilter(SearchFilter::class, properties: ['racerFullName' => 'partial', 'distance' => 'exact', 'ageCategory' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['racerFullName', 'distanceType', 'ageCategory', 'finishTime', 'overallPlacement', 'ageCategoryPlacement'])]
class RaceResults
{
    use Trait\IdTrait;

    #[ManyToOne(targetEntity: Race::class, inversedBy: 'raceResults')]
    #[Groups(['raceResults:write', 'raceResults:read'])]
    private Race $race;

    #[Column]
    #[Groups(['raceResults:write', 'raceResults:read'])]
    private string $racerFullName;

    #[Column()]
    #[Groups(['raceResults:write', 'raceResults:read'])]
    private DistanceEnum $distance;

    #[Column(type: 'integer', nullable: false)]
    #[Groups(['raceResults:write', 'raceResults:read'])]
    #[ApiProperty(openapiContext: [
        'type' => 'string',
        'example' => '5:22:40',
    ])]
    private int $finishTime;

    #[Column(nullable: false)]
    #[Groups(['raceResults:write', 'raceResults:read'])]
    private string $ageCategory;

    #[Column(type: 'integer', nullable: true)]
    #[Groups(['raceResults:read'])]
    private ?int $overallPlacement = null;

    #[Column(type: 'integer', nullable: true)]
    #[Groups(['raceResults:read'])]
    private ?int $ageCategoryPlacement = null;

    public function __construct()
    {
        $this->initializeId();
    }

    public function getRace(): Race
    {
        return $this->race;
    }

    public function setRace(Race $race): void
    {
        $this->race = $race;
    }

    public function getRacerFullName(): string
    {
        return $this->racerFullName;
    }

    public function setRacerFullName(string $racerFullName): void
    {
        $this->racerFullName = $racerFullName;
    }

    public function getDistance(): DistanceEnum
    {
        return $this->distance;
    }

    public function setDistance(DistanceEnum $distance): void
    {
        $this->distance = $distance;
    }

    public function getFinishTime(): int
    {
        return $this->finishTime;
    }

    public function setFinishTime(int $finishTime): void
    {
        $this->finishTime = $finishTime;
    }

    public function getAgeCategory(): string
    {
        return $this->ageCategory;
    }

    public function setAgeCategory(string $ageCategory): void
    {
        $this->ageCategory = $ageCategory;
    }

    public function getOverallPlacement(): ?int
    {
        return $this->overallPlacement;
    }

    public function setOverallPlacement(?int $overallPlacement): void
    {
        $this->overallPlacement = $overallPlacement;
    }

    public function getAgeCategoryPlacement(): ?int
    {
        return $this->ageCategoryPlacement;
    }

    public function setAgeCategoryPlacement(?int $ageCategoryPlacement): void
    {
        $this->ageCategoryPlacement = $ageCategoryPlacement;
    }

    #[Groups(['raceResults:read'])]
    #[SerializedName('finishTime')]
    public function getFinishTimeFormatted(): string
    {
        return FormatIntervalService::formatIntervalFromSecondsToString($this->finishTime);
    }
}
