<?php

namespace Kraken\WarmBundle\Calculator;

use Doctrine\ORM\EntityManager;

use Kraken\WarmBundle\Service\InstanceService;

class HeatingSeason
{
    protected $instance;
    protected $em;
    protected $locator;
    protected $seasonLength;
    protected $avgTemp;
    protected $climate;

    const HEATING_SEASON_THRESHOLD = 8;

    public function __construct(InstanceService $instance, EntityManager $em, NearestCityLocator $locator, ClimateZoneService $climate)
    {
        $this->instance = $instance->get();
        $this->em = $em;
        $this->locator = $locator;
        $this->seasonLength = null;
        $this->lastSeasonLength = null;
        $this->avgTemp = null;
        $this->lastYearAvgTemp = null;
        $this->climate = $climate;
    }

    public function getSeasonLength()
    {
        if ($this->seasonLength === null) {
            $this->seasonLength = count($this->getDailyTemperatures());
        }

        return $this->seasonLength;
    }

    public function getLastSeasonLength()
    {
        if ($this->lastSeasonLength === null) {
            $this->lastSeasonLength = count($this->getLastYearDailyTemperatures());
        }

        return $this->lastSeasonLength;
    }

    public function getDailyTemperatures()
    {
        return $this->em
            ->createQueryBuilder()
            ->select('t')
            ->from('KrakenWarmBundle:Temperature', 't')
            ->where('t.type = ?0')
            ->andWhere('t.value < ?1')
            ->andWhere('t.city = ?2')
            ->setParameters(array(
                0 => 'average',
                1 => self::HEATING_SEASON_THRESHOLD,
                2 => $this->locator->findNearestCity($this->instance),
            ))
            ->getQuery()
            ->getResult();
    }

    public function getLastYearDailyTemperatures()
    {
        return $this->em
            ->createQueryBuilder()
            ->select('t')
            ->from('KrakenWarmBundle:Temperature', 't')
            ->where('t.type = ?0')
            ->andWhere('t.value < ?1')
            ->andWhere('t.city = ?2')
            ->setParameters(array(
                0 => 'yearly',
                1 => self::HEATING_SEASON_THRESHOLD,
                2 => $this->locator->findNearestCity($this->instance),
            ))
            ->getQuery()
            ->getResult();
    }

    /*
     * Average temperature of average heating season.
     */
    public function getAverageTemperature()
    {
        if ($this->avgTemp === null) {
            $result = $this->em
                ->createQueryBuilder()
                ->select('AVG(t.value) as avgTemp')
                ->from('KrakenWarmBundle:Temperature', 't')
                ->where('t.type = ?0')
                ->andWhere('t.value < ?1')
                ->andWhere('t.city = ?2')
                ->setParameters(array(
                    0 => 'average',
                    1 => self::HEATING_SEASON_THRESHOLD,
                    2 => $this->locator->findNearestCity($this->instance),
                ))
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();

            $this->avgTemp = round($result['avgTemp'], 2);
        }

        return $this->avgTemp;
    }

    /*
     * Average temperature of heating season.
     */
    public function getLastYearAverageTemperature()
    {
        if ($this->lastYearAvgTemp === null) {
            $result = $this->em
                ->createQueryBuilder()
                ->select('AVG(t.value) as avgTemp')
                ->from('KrakenWarmBundle:Temperature', 't')
                ->where('t.type = ?0')
                ->andWhere('t.value < ?1')
                ->andWhere('t.city = ?2')
                ->setParameters(array(
                    0 => 'yearly',
                    1 => self::HEATING_SEASON_THRESHOLD,
                    2 => $this->locator->findNearestCity($this->instance),
                ))
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();

            $this->lastYearAvgTemp = round($result['avgTemp'], 2);
        }

        return $this->lastYearAvgTemp;
    }

    public function getLowestTemperature()
    {
        return $this->climate->getDesignOutdoorTemperature();
    }
}
