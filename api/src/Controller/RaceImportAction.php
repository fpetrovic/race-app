<?php

declare(strict_types=1);

namespace App\Controller;

use App\ApiResource\RaceImport;
use App\Service\RaceImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class RaceImportAction extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly RaceImportService $raceImportService,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $raceImport = new RaceImport(
                $request->files->get('file'),
                $request->request->get('title'),
                new \DateTimeImmutable($request->request->get('race_date'))
            );

            $this->validator->validate($raceImport);

            $this->raceImportService->import($raceImport);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'success'], Response::HTTP_CREATED);
    }
}
