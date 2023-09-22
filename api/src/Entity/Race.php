<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Trait\IdTrait;
use App\Service\FormatIntervalService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['race:list'],
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial'])]
#[ApiFilter(OrderFilter::class,
    properties: ['title', 'raceDate', 'averageFinishTimeForMediumDistance', 'averageFinishTimeForLongDistance'],
    arguments: ['orderParameterName' => 'order']
)]
class Race
{
    use IdTrait;

    #[Column(nullable: false)]
    #[Assert\NotBlank(message: 'Race title is required')]
    #[Groups(['race:list'])]
    private ?string $title = null;

    #[Column(type: 'date_immutable', nullable: false)]
    #[Assert\NotBlank(message: 'Race date is required')]
    #[Groups(['race:list'])]
    private \DateTimeImmutable $raceDate;

    #[Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private int $averageFinishTimeForLongDistance = 0;

    #[Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private int $averageFinishTimeForMediumDistance = 0;

    /**
     * @var Collection<int, RaceResults>
     * */
    #[OneToMany(mappedBy: 'race', targetEntity: RaceResults::class)]
    private Collection $raceResults;

    public function __construct()
    {
        $this->initializeId();
        $this->raceResults = new ArrayCollection();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getRaceDate(): ?\DateTimeImmutable
    {
        return $this->raceDate;
    }

    public function setRaceDate(?\DateTimeImmutable $raceDate): void
    {
        $this->raceDate = $raceDate;
    }

    public function getAverageFinishTimeForLongDistance(): int
    {
        return $this->averageFinishTimeForLongDistance;
    }

    public function setAverageFinishTimeForLongDistance(int $averageFinishTimeForLongDistance): void
    {
        $this->averageFinishTimeForLongDistance = $averageFinishTimeForLongDistance;
    }

    public function getAverageFinishTimeForMediumDistance(): int
    {
        return $this->averageFinishTimeForMediumDistance;
    }

    public function setAverageFinishTimeForMediumDistance(int $averageFinishTimeForMediumDistance): void
    {
        $this->averageFinishTimeForMediumDistance = $averageFinishTimeForMediumDistance;
    }

    public function getRaceResults(): Collection
    {
        return $this->raceResults;
    }

    public function addRaceResults(RaceResults $raceResults): void
    {
        $this->raceResults->add($raceResults);
    }

    public function removeRaceResults(RaceResults $raceResults): void
    {
        $this->raceResults->removeElement($raceResults);
    }

    #[Groups(['race:list'])]
    #[SerializedName('averageFinishTimeForLongDistance')]
    public function getAverageFinishTimeForLongDistanceFormatted(): string
    {
        return FormatIntervalService::formatIntervalFromSecondsToString($this->averageFinishTimeForLongDistance);
    }

    #[Groups(['race:list'])]
    #[SerializedName('averageFinishTimeForMediumDistance')]
    public function getAverageFinishTimeForMediumDistanceFormatted(): string
    {
        return FormatIntervalService::formatIntervalFromSecondsToString($this->averageFinishTimeForMediumDistance);
    }
}
