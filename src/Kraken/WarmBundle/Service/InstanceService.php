<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\Calculation;

class InstanceService
{
    protected $calculation = null;

    public function setCalculation(Calculation $calc)
    {
        $this->calculation = $calc;
    }

    public function get()
    {
        if (!$this->calculation instanceof Calculation) {
            throw new \RuntimeException('There is no Calculation instance here');
        }

        return $this->calculation;
    }
}
