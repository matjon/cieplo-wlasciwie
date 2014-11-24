<?php

namespace Kraken\WarmBundle\Calculator;

use Kraken\WarmBundle\Service\InstanceService;

class ClimateZoneService
{
    protected $instance;

    protected $designTemperatures = array(
        1 => -16,
        2 => -18,
        3 => -20,
        4 => -22,
        5 => -24,
    );

    public function __construct(InstanceService $instance)
    {
        $this->instance = $instance->get();
    }

    public function getClimateZone()
    {
        $lat = $this->instance->getLatitude();
        $lon = $this->instance->getLongitude();

        if (
            ($lat >= 49.15 && $lat <= 49.41 && $lon >= 19.79 && $lon <= 20.31) ||
            ($lat >= 53.65 && $lat <= 54.37 && $lon >= 21.80 && $lon <= 23.70)
        )
        {
            return 5;
        }

        if (
            ($lat >= 48.90 && $lat <= 49.46 && $lon >= 18.36 && $lon <= 23.05) ||
            ($lat >= 51.78 && $lat <= 54.39 && $lon >= 21.85 && $lon <= 24.07) ||
            ($lat >= 53.18 && $lat <= 54.39 && $lon >= 20.25 && $lon <= 21.85)
        )
        {
            return 4;
        }

        if (
            ($lat >= 52.57 && $lat <= 54.14 && $lon >= 14.10 && $lon <= 19.40) ||
            ($lat >= 53.81 && $lat <= 54.96 && $lon >= 15.49 && $lon <= 19.55)
        )
        {
            return 1;
        }

        if ($lat >= 51.00 && $lat <= 54.85 && $lon >= 14.10 && $lon <= 19.50) {
            return 2;
        }

        return 3;
    }

    public function getDesignOutdoorTemperature()
    {
        return $this->designTemperatures[$this->getClimateZone()];
    }
}
