<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Service\FuelService;

class FuelServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testFuelEnergy()
    {
        $fs = new FuelService();

        $this->assertEquals(15540, $fs->getFuelEnergy('coal', 2));
        $this->assertEquals(33300, $fs->getFuelEnergy('sand_coal', 5));
        $this->assertEquals(10000, $fs->getFuelEnergy('brown_coal', 2));
        $this->assertEquals(60000, $fs->getFuelEnergy('pellet', 12));
        $this->assertEquals(21.1, $fs->getFuelEnergy('gas_e', 2));
        $this->assertEquals(77.7, $fs->getFuelEnergy('gas_ls', 10));
        $this->assertEquals(86.1, $fs->getFuelEnergy('gas_lw', 10));
        $this->assertEquals(23400, $fs->getFuelEnergy('wood', 10));
        $this->assertEquals(3, $fs->getFuelEnergy('electricity', 3));

        $this->setExpectedException('InvalidArgumentException');
        $this->assertEquals(10, $fs->getFuelEnergy('shmoal', 10));
    }
}
