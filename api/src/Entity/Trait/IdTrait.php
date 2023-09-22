<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

trait IdTrait
{
    #[ Id,
        Column(type: UuidType::NAME, unique: true),
        GeneratedValue(strategy: 'NONE')
    ]
    protected Uuid $id;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function initializeId(): void
    {
        $this->id ??= Uuid::v4();
    }
}
