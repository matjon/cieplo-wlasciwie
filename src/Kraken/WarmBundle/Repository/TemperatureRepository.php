<?php

namespace Kraken\WarmBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kraken\WarmBundle\Entity\City;

class TemperatureRepository extends EntityRepository
{
    public function getLastWinterTemperatures(City $city)
    {
        return $this->_em->createQueryBuilder()
            ->select('t')
            ->from('KrakenWarmBundle:Temperature', 't')
            ->where('t.type = ?1')
            ->andWhere('t.city = ?2')
            ->groupBy('t.month', 't.day')
            ->orderBy('t.id')
            ->setParameter(1, 'yearly')
            ->setParameter(2, $city)
            ->getQuery()
            ->getResult();
    }

    public function getAverageWinterTemperatures(City $city)
    {
        return $this->_em->createQueryBuilder()
            ->select('t')
            ->from('KrakenWarmBundle:Temperature', 't')
            ->where('t.type = ?1')
            ->andWhere('t.city = ?2')
            ->groupBy('t.month', 't.day')
            ->orderBy('t.id')
            ->setParameter(1, 'average')
            ->setParameter(2, $city)
            ->getQuery()
            ->getResult();
    }
}
