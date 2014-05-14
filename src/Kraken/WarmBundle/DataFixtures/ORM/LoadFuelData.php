<?php

namespace Kraken\WarmBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kraken\WarmBundle\Entity\Fuel;

class LoadFuelData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = file_get_contents(dirname(__DIR__) . '/fuels.json');

        foreach (json_decode($data, true) as $fuel) {
            $f = new Fuel();
            $f->setType($fuel['type']);
            $f->setName($fuel['label']);
            $f->setDetail($fuel['detail']);
            $f->setPrice($fuel['price']);
            $f->setUnit($fuel['unit']);
            $f->setTradeAmount($fuel['trade_amount']);
            $f->setTradeUnit($fuel['trade_unit']);
            $f->setEnergy($fuel['energy']);
            $f->setEfficiency($fuel['efficiency']);

            $manager->persist($f);
        }

        $manager->flush();
    }
}
