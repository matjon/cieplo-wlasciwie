<?php

namespace Kraken\WarmBundle\Calculator;

use Doctrine\ORM\EntityManager;
use Kraken\WarmBundle\Service\InstanceService;

class NearestCityLocator
{
    protected $instance;
    protected $em;

    public function __construct(InstanceService $instance, EntityManager $em)
    {
        $this->instance = $instance->get();
        $this->em = $em;
    }

    public function findNearestCity()
    {
        $lat = $this->instance->getLatitude();
        $lon = $this->instance->getLongitude();

        $distance = '(6371 * acos(cos(radians('.$lat.')) * cos(radians(c.latitude)) * '
            .'cos(radians(c.longitude) - radians('.$lon.')) + sin(radians('.$lat.')) '
            .'* sin(radians(c.latitude)))) AS distance';

        $nearestCity = $this->em
            ->createQueryBuilder()
            ->select('c.id, '.$distance)
            ->from('KrakenWarmBundle:City', 'c')
            ->orderBy('distance', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        return $this->em->getRepository('KrakenWarmBundle:City')
            ->findOneById($nearestCity['id']);
    }
}
