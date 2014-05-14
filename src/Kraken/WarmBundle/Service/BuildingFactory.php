<?php

namespace Kraken\WarmBundle\Service;

class BuildingFactory
{
    public function get(InstanceService $instance, VentilationService $ventilation, WallService $wall, WallFactory $wall_factory)
    {
        $calc = $instance->get();

        if ($calc->getBuildingType() == 'apartment') {
            $building = new Apartment($instance, $ventilation, $wall, $wall_factory);
        } elseif ($calc->getBuildingType() == 'row_house') {
            $building = new RowBuilding($instance, $ventilation, $wall, $wall_factory);
        } elseif ($calc->getBuildingType() == 'double_house') {
            $building = new DoubleBuilding($instance, $ventilation, $wall, $wall_factory);
        } else {
            $building = new Building($instance, $ventilation, $wall, $wall_factory);
        }

        return $building;
    }
}
