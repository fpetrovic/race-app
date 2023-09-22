<?php

namespace App\Serializer;

use App\Entity\RaceResults;
use App\Service\FormatIntervalService;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RaceResultsDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    /**
     * @param mixed|null $format
     *
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $data['finishTime'] = FormatIntervalService::formatIntervalFromStringToSeconds($data['finishTime']);

        return $this->denormalizer->denormalize($data, RaceResults::class, $format, $context + [__CLASS__ => true]);
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return \in_array($format, ['json', 'jsonld'], true) && is_a($type, RaceResults::class, true) && !isset($context[__CLASS__]);
    }
}
