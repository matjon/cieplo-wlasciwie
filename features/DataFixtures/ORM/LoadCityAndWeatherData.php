<?php

namespace DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kraken\WarmBundle\Entity\City;
use Kraken\WarmBundle\Entity\Temperature;

class LoadWeatherData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = array(
            "PLXX0017" => array(
                "name" => "Otwock",
                "lat" => 52.1053186,
                "lon" => 21.2616248,
            )
        );

        foreach ($data as $code => $cityData) {
            if (isset($cityData['error'])) {
                continue;
            }

            $city = new City();
            $city->setName($cityData['name']);
            $city->setLatitude($cityData['lat']);
            $city->setLongitude($cityData['lon']);
            $manager->persist($city);

            $cache = dirname(__DIR__) . '/../../src/Kraken/WarmBundle/DataFixtures/weather/'.$code.'.json';
            if (file_exists($cache)) {
                $temperatures = json_decode(file_get_contents($cache), true);
            } else {
                throw new \RuntimeException('No weather data for '.$code);
            }

            foreach ($temperatures as $month => $monthData) {
                foreach ($monthData as $day => $value) {
                    $t = new Temperature();
                    $t->setMonth($month);
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
