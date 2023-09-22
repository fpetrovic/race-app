<?php

declare(strict_types=1);

namespace Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\RaceFactory;
use App\Factory\RaceResultsFactory;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class RaceResultsTest extends ApiTestCase
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
    public function testGetRaceResults()
    {
        RaceFactory::createMany(15);
        RaceResultsFactory::createMany(150);

        $raceCollectionResponse = static::createClient()->request('GET', '/api/races');
        $raceCollectionResponseArray = $raceCollectionResponse->toArray();
        $raceItem = $raceCollectionResponseArray['hydra:member'][0];

        $raceResultsCollectionResponse = static::createClient()->request('GET', sprintf('/api/races/%s/race-results', $raceItem['id']));
        $raceResultsCollectionResponseArray = $raceResultsCollectionResponse->toArray();

        $this->assertResponseStatusCodeSame(200);

        $raceResultItem = $raceResultsCollectionResponseArray['hydra:member'][0];
        $this->assertArrayHasKey('racerFullName', $raceResultItem);
        $this->assertArrayHasKey('ageCategory', $raceResultItem);
        $this->assertArrayHasKey('distance', $raceResultItem);
        $this->assertArrayHasKey('ageCategoryPlacement', $raceResultItem);
        $this->assertArrayHasKey('overallPlacement', $raceResultItem);
        $this->assertArrayHasKey('finishTime', $raceResultItem);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testGetRaceResultsFilter()
    {
        RaceFactory::createMany(15);
        RaceResultsFactory::createMany(150);

        $raceCollectionResponse = static::createClient()->request('GET', '/api/races');
        $raceCollectionResponseArray = $raceCollectionResponse->toArray();
        $raceItem = $raceCollectionResponseArray['hydra:member'][0];

        $raceResultsCollectionResponse = static::createClient()->request('GET', sprintf('/api/races/%s/race-results?distance=medium', $raceItem['id']));
        $raceResultsCollectionResponseArray = $raceResultsCollectionResponse->toArray();

        for ($i = 0; $i < (int) $raceResultsCollectionResponseArray['hydra:totalItems']; ++$i) {
            $this->assertEquals('medium', $raceResultsCollectionResponseArray['hydra:member'][$i]['distance']);
        }
    }

    /**
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testEditRaceResultsItem()
    {
        RaceFactory::createMany(15);
        RaceResultsFactory::createMany(150);

        $raceCollectionResponse = static::createClient()->request('GET', '/api/races');
        $raceCollectionResponseArray = $raceCollectionResponse->toArray();
        $raceItem = $raceCollectionResponseArray['hydra:member'][0];

        $raceResultsCollectionResponse = static::createClient()->request('GET', sprintf('/api/races/%s/race-results?distance=medium', $raceItem['id']));
        $raceResultsCollectionResponseArray = $raceResultsCollectionResponse->toArray();

        $raceResultsItem = $raceResultsCollectionResponseArray['hydra:member'][0];

        $updatedRaceResults = [];

        $updatedRaceResults['racerFullName'] = 'Jon Jones';
        $updatedRaceResults['distance'] = 'long';
        $updatedRaceResults['ageCategory'] = 'M34-45';
        $updatedRaceResults['finishTime'] = '3:25:45';

        $raceResultsItemUpdateResponse = static::createClient()->request(
            'PATCH',
            sprintf('/api/race-results/%s', $raceResultsItem['id']),
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'accept' => 'application/ld+json',
                ],
                'json' => $updatedRaceResults, ]
        );

        $raceResultsItemUpdateResponseArray = $raceResultsItemUpdateResponse->toArray();

        $this->assertEquals('Jon Jones', $raceResultsItemUpdateResponseArray['racerFullName']);
        $this->assertEquals('long', $raceResultsItemUpdateResponseArray['distance']);
        $this->assertEquals('M34-45', $raceResultsItemUpdateResponseArray['ageCategory']);
        $this->assertEquals('3:25:45', $raceResultsItemUpdateResponseArray['finishTime']);
    }
}
