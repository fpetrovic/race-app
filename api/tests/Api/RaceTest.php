<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\RaceFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class RaceTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetRaces(): void
    {
        RaceFactory::createMany(15);

        $response = static::createClient()->request('GET', '/api/races');
        $responseArray = $response->toArray();

        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals(15, $responseArray['hydra:totalItems']);

        $raceItem = $responseArray['hydra:member'][0];
        $this->assertArrayHasKey('title', $raceItem);
        $this->assertArrayHasKey('raceDate', $raceItem);
        $this->assertArrayHasKey('averageFinishTimeForMediumDistance', $raceItem);
        $this->assertArrayHasKey('averageFinishTimeForLongDistance', $raceItem);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetRacesFiltered(): void
    {
        RaceFactory::createMany(15);

        $response = static::createClient()->request('GET', '/api/races?title=15');
        $responseArray = $response->toArray();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals(1, $responseArray['hydra:totalItems']);

        $raceItem = $responseArray['hydra:member'][0];
        $this->assertEquals('race 15', $raceItem['title']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetRacesOrdered(): void
    {
        RaceFactory::createMany(15);

        $response = static::createClient()->request('GET', 'api/races?order%5BaverageFinishTimeForMediumDistance%5D=desc');
        $responseArray = $response->toArray();

        for ($i = 1; $i < 15; ++$i) {
            $this->assertTrue(
                $responseArray['hydra:member'][$i - 1]['averageFinishTimeForMediumDistance'] >=
                $responseArray['hydra:member'][$i]['averageFinishTimeForMediumDistance']
            );
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testImport(): void
    {
        $uploadedFile = new UploadedFile(
            __DIR__.'/../output.csv',
            'output.csv',
            'text/csv',
            null,
            true
        );

        $response = static::createClient()->request(
            'POST',
            '/api/race-import',
            [
                'headers' => [
                    'accept' => 'application/ld+json',
                    'Content-Type' => 'multipart/form-data',
                ],
                'extra' => [
                    'files' => [
                        'file' => $uploadedFile,
                    ],
                    'parameters' => [
                        'title' => 'race',
                        'race_date' => '2222-2-2',
                    ],
                ],
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = static::createClient()->request('GET', '/api/races?title=race');
        $responseArray = $response->toArray();

        $importedRaceItem = $responseArray['hydra:member'][0];

        $raceResultsCollectionResponse = static::createClient()->request(
            'GET',
            sprintf('/api/races/%s/race-results', $importedRaceItem['id']
            )
        );

        $raceResultsCollectionResponseArray = $raceResultsCollectionResponse->toArray();
        $this->assertEquals(20000, $raceResultsCollectionResponseArray['hydra:totalItems']);
    }
}
