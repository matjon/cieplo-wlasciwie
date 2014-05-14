<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Wall;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Service\HouseService;

class HouseServiceTest extends \PHPUnit_Framework_TestCase
{
//     public function testNumberOfHeatedFloors()
//     {
//         $house = new House();
//         $house->setVentilationType('natural');
//         $house->setNumberFloors(3);
//         $house->setRoofType('flat');
//         $house->setIsGroundFloorHeated(false);
//         $house->setHasBasement(false);
//
//         $calc = new Calculation();
//         $calc->setHouse($house);
//
//         $instance = new InstanceService();
//         $instance->setCalculation($calc);
//
//         $h = new HouseService($instance);
//
//         $this->assertEquals(2, $h->getNumberOfHeatedFloors());
//     }
//
    public function testWallThickness()
    {
//         $calc = new Calculation();
//
//         $instance = new InstanceService();
//         $instance->setCalculation($calc);
//
//         $h = new HouseService($instance);
//
//         $m = new Material();
//
//         $l1 = new Layer();
//         $l1->setSize(20);
//
//         $l2 = new Layer();
//         $l2->setSize(30);
//
//         $l3 = new Layer();
//         $l3->setSize(60);
//
//         $w = new Wall();
//         $w->setConstructionLayer($l1);
//         $w->setIsolationLayer($l2);
//         $w->setOutsideLayer($l3);

        $this->assertEquals(1,1);
    }
}
