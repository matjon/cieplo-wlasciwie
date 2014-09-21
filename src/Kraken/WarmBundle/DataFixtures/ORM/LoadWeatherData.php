<?php

namespace Kraken\WarmBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kraken\WarmBundle\Entity\City;
use Kraken\WarmBundle\Entity\Temperature;

class LoadWeatherData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = file_get_contents(dirname(__DIR__) . '/cities.json');

        $data = json_decode($data, true);
        if (empty($data)) {
            throw new \RuntimeException("List of cities seems to be broken.");
        }

        foreach ($data as $code => $cityData) {
            if (isset($cityData['error'])) {
                continue;
            }

            $city = $manager->getRepository('KrakenWarmBundle:City')->findOneByName($cityData['name']);

            if (!$city) {
                continue;
            }

            $cache = dirname(__DIR__) . '/weather/'.$code.'.json';
            if (file_exists($cache)) {
                $temperatures = json_decode(file_get_contents($cache), true);
            } else {
                throw new \RuntimeException('No weather data for '.$code);
            }

            foreach ($temperatures as $month => $monthData) {
                foreach ($monthData as $day => $value) {
                    if ($month > 4 && $month < 9) {
                        continue;
                    }

                    $t = new Temperature();
                    $t->setMonth($month);
                    $t->setType('average');
                    $t->setDay($day);
                    $t->setValue($value);
                    $t->setCity($city);
                    $manager->persist($t);
                }
            }
        }

        $manager->flush();
    }
}
