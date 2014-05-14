<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Wall;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Service\WallService;

class WallServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testThermalConductance()
    {
        $house = new House();
        $house->setVentilationType('natural');

        $calc = new Calculation();
        $calc->setHouse($house);
        $calc->setIndoorTemperature(20);
        $instance = new InstanceService();
        $instance->setCalculation($calc);

        $service = new WallService($instance);

        $m1 = new Material;
        $m1->setLambda(0.56);
        $m2 = new Material;
        $m2->setLambda(0.04);

        $l1 = new Layer;
        $l1->setSize(40);
        $l1->setMaterial($m1);

        $l2 = new Layer;
        $l2->setSize(12);
        $l2->setMaterial($m2);

        $w = new Wall();
        $w->setConstructionLayer($l1);

        $this->assertEquals(1.4, $service->getThermalConductance($w));

        $w->setIsolationLayer($l2);

        $this->assertEquals(0.27, $service->getThermalConductance($w));
    }

    public function testThermalConductanceWithAirIsolation()
    {
        $house = new House();
        $calc = new Calculation();
        $calc->setHouse($house);
        $instance = new InstanceService();
        $instance->setCalculation($calc);

        $service = new WallService($instance);

        $m1 = new Material;
        $m1->setName('Pustka powietrzna');

        $l1 = new Layer;
        $l1->setSize(5);
        $l1->setMaterial($m1);

        $m2 = new Material;
        $m2->setLambda(0.56);

        $l2 = new Layer;
        $l2->setSize(40);
        $l2->setMaterial($m2);

        $w = new Wall();
        $w->setConstructionLayer($l2);

        $this->assertEquals(1.4, $service->getThermalConductance($w));

        $w->setIsolationLayer($l1);

        $this->assertEquals(1.12, $service->getThermalConductance($w));
    }

    public function testSize()
    {
        $house = new House();
        $house->setVentilationType('natural');

        $calc = new Calculation();
        $calc->setHouse($house);
        $calc->setIndoorTemperature(20);
        $instance = new InstanceService();
        $instance->setCalculation($calc);

        $service = new WallService($instance);

        $m1 = new Material;
        $m1->setLambda(0.56);
        $m2 = new Material;
        $m2->setLambda(0.04);
        $m3 = new Material;
        $m3->setLambda(0.82);

        $l1 = new Layer;
        $l1->setSize(25);
        $l1->setMaterial($m1);

        $l2 = new Layer;
        $l2->setSize(12);
        $l2->setMaterial($m2);

        $l3 = new Layer;
        $l3->setSize(8);
        $l3->setMaterial($m3);

        $w = new Wall();
        $w->setConstructionLayer($l1);
        $w->setIsolationLayer($l2);
        $w->setOutsideLayer($l3);

        $this->assertEquals(0.5, $service->getSize($w));
    }
}
