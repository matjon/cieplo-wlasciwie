<?php

namespace DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kraken\WarmBundle\Entity\Material;

class LoadMaterialData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = file_get_contents(dirname(__DIR__) . '/../../src/Kraken/WarmBundle/DataFixtures/materials.json');

        foreach (json_decode($data, true) as $material) {
            $m = new Material();
            $m->setName($material['name']);
            $m->setLambda($material['lambda']);

            if (isset($material['for_wall_construction_layer']))
                $m->setForWallConstructionLayer($material['for_wall_construction_layer']);
            if (isset($material['for_wall_internal_layer']))
                $m->setForWallInternalLayer($material['for_wall_internal_layer']);
            if (isset($material['for_wall_facade_layer']))
                $m->setForWallFacadeLayer($material['for_wall_facade_layer']);
            if (isset($material['for_wall_isolation_layer']))
                $m->setForWallIsolationLayer($material['for_wall_isolation_layer']);
            if (isset($material['for_ceiling']))
                $m->setForCeiling($material['for_ceiling']);
            if (isset($material['for_floor']))
                $m->setForFloor($material['for_floor']);

            $manager->persist($m);
        }

        $manager->flush();
    }
}
