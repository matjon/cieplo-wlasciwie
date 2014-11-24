<?php

namespace Kraken\WarmBundle\Service;

class FuelService
{
    protected $fuelsEnergy = array(
        'coal' => 7.77,
        'coke' => 8,
        'sand_coal' => 6.66,
        'brown_coal' => 5,
        'pellet' => 5,
        'gas_e' => 10.55,
        'gas_ls' => 7.77,
        'gas_lw' => 8.61,
        'wood' => 5,
        'electricity' => 1,
    );

    /**
     * @param string $fuelType
     */
    public function getFuelEnergy($fuelType, $amount)
    {
        if (!isset($this->fuelsEnergy[$fuelType])) {
            throw new \InvalidArgumentException('Invalid fuel type');
        }

        return $this->fuelsEnergy[$fuelType] * $this->consumedAmountToBaseUnits($fuelType, $amount);
    }

    public function consumedAmountToBaseUnits($fuelType, $amount)
    {
        // tons to kgs
        if (in_array($fuelType, array('coal', 'coke', 'sand_coal', 'brown_coal', 'pellet'))) {
            return $amount * 1000;
        }

        // stere ~ 0,65 cubic meter
        if ($fuelType == 'wood') {
            return 0.65 * $amount * 720; // 720kg/
        }

        return $amount;
    }

}
