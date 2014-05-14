<?php

namespace Kraken\WarmBundle\Map;

use Vich\GeographicalBundle\Map\Map;

/**
 * LocationMap.
 */
class LocationMap extends Map
{
    /**
     * Constructs a new instance of LocationMap.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setZoom(6);
        $this->setContainerId('map_canvas');
        $this->setShowZoomControl(true);
        $this->setCenter(51.917168, 19.138184);
        $this->setWidth(600);
        $this->setHeight(450);
    }
}
