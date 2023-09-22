<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\RaceImportAction;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/race-import',
            controller: RaceImportAction::class,
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                    'title' => [
                                        'type' => 'string',
                                    ],
                                    'race_date' => [
                                        'type' => 'string',
                                        'format' => 'date',
                                    ],
                                ],
                            ],
                        ],
                    ])
                )
            ),
            deserialize: false
        ),
    ]
)]
readonly class RaceImport
{
    public function __construct(File $file, string $title, \DateTimeImmutable $raceDate)
    {
        $this->file = $file;
        $this->title = $title;
        $this->raceDate = $raceDate;
    }
    #[Assert\NotBlank]
    #[Assert\File(maxSize: '5M', mimeTypes: ['text/csv', 'text/plain'])]
    private File $file;

    #[Assert\NotBlank]
    private string $title;

    #[Assert\NotBlank]
    private \DateTimeImmutable $raceDate;

    public function getFile(): File
    {
        return $this->file;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRaceDate(): \DateTimeImmutable
    {
        return $this->raceDate;
    }
}
