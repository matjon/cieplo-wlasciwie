<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Calculator\BuildingInterface;
use Kraken\WarmBundle\Service\InstanceService;

class UpgradeService
{
    protected $instance;
    protected $building;
    protected $wall;

    public function __construct(InstanceService $instance, BuildingInterface $building)
    {
        $this->instance = $instance->get();
        $this->building = $building;
    }

    public function getVariants()
    {
        $variants = array();

        $actualEnergyLoss = $this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated();

        $this->tryBetterWallIsolation($actualEnergyLoss, $variants);
        $this->tryBetterRoofIsolation($actualEnergyLoss, $variants);
        $this->tryNewWindows($actualEnergyLoss, $variants);
        $this->tryNewDoors($actualEnergyLoss, $variants);
        $this->tryBetterGroundFloorCeilingIsolation($actualEnergyLoss, $variants);
        $this->tryBetterGroundFloorIsolation($actualEnergyLoss, $variants);

        $apartment = $this->instance->getHouse()->getApartment();

        if ($apartment) {
            $this->tryApartmentFloorIsolation($apartment, $actualEnergyLoss, $variants);
            $this->tryApartmentCeilingIsolation($apartment, $actualEnergyLoss, $variants);
            $this->tryApartmentWallsIsolation($apartment, $actualEnergyLoss, $variants);
        }

        $gain = array();
        foreach ($variants as $key => $row)
        {
            if ($row['gain'] < 0.05) {
                unset($variants[$key]);
                continue;
            }

            $gain[$key] = $row['gain'];
        }

        array_multisort($gain, SORT_DESC, $variants);

        return $variants;
    }

    protected function tryApartmentWallsIsolation($apartment, $actualEnergyLoss, array &$variants)
    {
        $instance = clone unserialize(serialize($this->instance));
        $house = $instance->getHouse();
  
        if ($apartment->getNumberUnheatedWalls() > 0) {
            $building = clone $this->building;
            $building->setInstance($instance);

            $newEnergyLoss = $building->getEnergyLossToOutside() + $building->getEnergyLossToUnheated(true);

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => 'ocieplenie ścian od pomieszczeń nieogrzewanych 5cm styropianu'
            );
        }
    }

    protected function tryApartmentCeilingIsolation($apartment, $actualEnergyLoss, array &$variants)
    {
        $instance = clone unserialize(serialize($this->instance));
        $house = $instance->getHouse();

        if ($apartment->getWhatsOver() != 'heated_room') {
            $ceilingIsolation = $house->getHighestCeilingIsolationLayer();

            if (!$ceilingIsolation || $ceilingIsolation->getSize() <= 5) {
                $m = new Material();
                $m->setLambda(0.038);

                $l = new Layer();
                $l->setMaterial($m);
                $l->setSize(10);

                $house->setHighestCeilingIsolationLayer($l);
                
                $building = clone $this->building;
                $building->setInstance($instance);
                $newEnergyLoss = $building->getEnergyLossToOutside() + $building->getEnergyLossToUnheated();

                $variants[] = array(
                    'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                    'title' => 'ocieplenie sufitu 10cm styropianu'
                );
            }
        }
    }

    protected function tryApartmentFloorIsolation($apartment, $actualEnergyLoss, array &$variants)
    {
        $instance = clone unserialize(serialize($this->instance));
        $house = $instance->getHouse();

        if ($apartment->getWhatsUnder() != 'heated_room') {
            $ceilingIsolation = $house->getLowestCeilingIsolationLayer();

            if (!$ceilingIsolation || $ceilingIsolation->getSize() <= 5) {
                $m = new Material();
                $m->setLambda(0.038);

                $l = new Layer();
                $l->setMaterial($m);
                $l->setSize(10);

                $house->setLowestCeilingIsolationLayer($l);
                
                $building = clone $this->building;
                $building->setInstance($instance);
                $newEnergyLoss = $building->getEnergyLossToOutside() + $building->getEnergyLossToUnheated();

                $variants[] = array(
                    'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                    'title' => 'ocieplenie podłogi 10cm styropianu'
                );
            }
        }
    }

    protected function tryBetterGroundFloorIsolation($actualEnergyLoss, array &$variants)
    {
        $instance = clone unserialize(serialize($this->instance));
        $house = $instance->getHouse();

        if ($this->building->isGroundFloorHeated()) {
            $groundFloorIsolation = $house->getGroundFloorIsolationLayer();
            $materialName = $groundFloorIsolation && $groundFloorIsolation->getMaterial() ? $groundFloorIsolation->getMaterial()->getName() : '';

            if (!$groundFloorIsolation || $groundFloorIsolation->getSize() <= 10 || (!stristr($materialName, 'styropian') && !stristr($materialName, 'wełna'))) {
                $m = new Material();
                $m->setLambda(0.038);

                $l = new Layer();
                $l->setMaterial($m);
                $l->setSize(20);

                $house->setGroundFloorIsolationLayer($l);
                
                $building = clone $this->building;
                $building->setInstance($instance);
                $newEnergyLoss = $building->getEnergyLossToOutside() + $building->getEnergyLossToUnheated();

                $variants[] = array(
                    'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                    'title' => 'ocieplenie podłogi parteru 20cm styropianu'
                );
            }
        }
    }

    protected function tryBetterGroundFloorCeilingIsolation($actualEnergyLoss, array &$variants)
    {
        $instance = clone unserialize(serialize($this->instance));
        $house = $instance->getHouse();

        if (!$this->building->isGroundFloorHeated()) {
            $ceilingIsolation = $house->getLowestCeilingIsolationLayer();

            if (!$ceilingIsolation || $ceilingIsolation->getSize() <= 5) {
                $m = new Material();
                $m->setLambda(0.038);

                $l = new Layer();
                $l->setMaterial($m);
                $l->setSize(10);

                $house->setLowestCeilingIsolationLayer($l);
                
                $building = clone $this->building;
                $building->setInstance($instance);
                $newEnergyLoss = $building->getEnergyLossToOutside() + $building->getEnergyLossToUnheated();

                $variants[] = array(
                    'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                    'title' => 'ocieplenie stropu nad parterem 10cm styropianu'
                );
            }
        }
    }

    protected function tryNewDoors($actualEnergyLoss, array &$variants)
    {
        $instance = clone unserialize(serialize($this->instance));
        $house = $instance->getHouse();
        $doorsType = $house->getDoorsType();

        if (stristr($doorsType, 'old')) {
            $house->setDoorsType('new_metal');
            
            $building = clone $this->building;
            $building->setInstance($instance);
            $newEnergyLoss = $building->getEnergyLossToOutside() + $building->getEnergyLossToUnheated();

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => 'wymiana drzwi'
            );
        }
    }

    protected function tryNewWindows($actualEnergyLoss, array &$variants)
    {
        $instance = clone unserialize(serialize($this->instance));
        $house = $instance->getHouse();
        $windowsType = $house->getWindowsType();

        if (stristr($windowsType, 'old')) {
            $house->setWindowsType('new_double_glass');
            
            $building = clone $this->building;
            $building->setInstance($instance);
            $newEnergyLoss = $building->getEnergyLossToOutside() + $building->getEnergyLossToUnheated();

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => 'wymiana wszystkich okien'
            );
        }
    }

    protected function tryBetterWallIsolation($actualEnergyLoss, array &$variants)
    {
        $originalWall = $this->instance->getHouse()->getWalls()->first();

        if (!$originalWall->getExtraIsolationLayer() || $originalWall->getExtraIsolationLayer()->getSize() < 10) {
            $isolationSize = 15;

            $instance = clone unserialize(serialize($this->instance));
            $wall = $instance->getHouse()->getWalls()->first();

            $m = new Material();
            $m->setLambda(0.038);

            $l = new Layer();
            $l->setMaterial($m);
            $l->setSize($isolationSize);

            $wall->setExtraIsolationLayer($l);
            
            $building = clone $this->building;
            $building->setInstance($instance);
            $newEnergyLoss = $building->getEnergyLossToOutside() + $building->getEnergyLossToUnheated();

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => sprintf('ocieplenie ścian zewn. %scm styropianu', $isolationSize)
            );
        }
    }

    protected function tryBetterRoofIsolation($actualEnergyLoss, array &$variants)
    {
        $instance = clone unserialize(serialize($this->instance));
        $house = $instance->getHouse();
        $roofType = $house->getRoofType();

        $flatRoof = $roofType == 'flat' || $roofType == false;

        $roofIsolation = $flatRoof ? $house->getHighestCeilingIsolationLayer() : $house->getRoofIsolationLayer();
        $materialName = $roofIsolation && $roofIsolation->getMaterial() ? $roofIsolation->getMaterial()->getName() : '';

        if (!$roofIsolation || $roofIsolation->getSize() < 20 || (!stristr($materialName, 'styropian') && !stristr($materialName, 'wełna'))) {
            $isolationSize = $flatRoof ? 35 : 20;
            
            $m = new Material();
            $m->setLambda(0.038);

            $l = new Layer();
            $l->setMaterial($m);
            $l->setSize($isolationSize);

            if ($flatRoof || !$this->building->isAtticHeated()) {
                $house->setHighestCeilingIsolationLayer($l);
            } else {
                $house->setRoofIsolationLayer($l);
            }
            
            $building = clone $this->building;
            $building->setInstance($instance);
            $newEnergyLoss = $building->getEnergyLossToOutside() + $building->getEnergyLossToUnheated();

            $variants[] = array(
                'gain' => round(($actualEnergyLoss - $newEnergyLoss) / $actualEnergyLoss, 2),
                'title' => sprintf('ocieplenie dachu %scm styropianu', $isolationSize)
            );
        }
    }
}
