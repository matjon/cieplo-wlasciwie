<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Wall;

class WallService
{
    protected $instance;

    public function __construct(InstanceService $instance)
    {
        $this->instance = $instance->get();
    }

    /**
      * Get thermal conductance factor, including all layers and stuffs
      */
    public function getThermalConductance(Wall $wall)
    {
        $thermalResistance = 0;

        foreach ($wall->getLayers() as $layer) {
            $lambda = $layer->getMaterial()->getLambda();
            $size = $layer->getSize() / 100;

            if (stristr($layer->getMaterial()->getName(), 'pustka')) {
                $thermalResistance += 0.18;
            } elseif ($size != 0 && $lambda != 0) {
                $thermalResistance += $size/$lambda;
            }
        }

        //TODO thermal bridges
        /*if ($this->instance->getHouse()->getHasBalcony()) {
            $thermalResistance += 1/0.15;
        } elseif ($this->instance->getHouse()->getNumberWindows() > 5) {
            $thermalResistance += 1/0.1;
        }*/

        return $thermalResistance > 0
            ? round(1/$thermalResistance, 2)
            : 0;
    }

    /**
      * Get wall size, including all layers and stuffs
      */
    public function getSize(Wall $wall)
    {
        $thickness = 0.05; // plasters and stuffs

        foreach ($wall->getLayers() as $layer) {
            $thickness += $layer->getSize()/100;
        }

        return $thickness;
    }
}
