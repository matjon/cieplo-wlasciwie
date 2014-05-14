<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="calculation")
 */
class Calculation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", scale=7)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="decimal", scale=7)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('single_house', 'double_house', 'row_house', 'apartment')")
     */
    protected $building_type;

    /**
     * @ORM\Column(type="integer", length=4)
     * @Assert\Range(min="1900", minMessage="Jeśli dom jest sprzed XX wieku, wybierz 1900 rok")
     */
    protected $construction_year;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(min="10", minMessage = "To zbyt niska temperatura dla budynku mieszkalnego", max="50", maxMessage = "To zbyt wysoka temperatura dla budynku mieszkalnego", invalidMessage="Nieprawidłowa wartość")
     */
    protected $indoor_temperature;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fuel_type;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\Range(min="1", minMessage = "Nie za mało?")
     */
    protected $fuel_consumption;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\Range(min="0.01", minMessage = "Nie za mało?")
     */
    protected $fuel_cost;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('manual_upward', 'manual_downward', 'automatic', 'fireplace', 'kitchen', 'ceramic', 'goat')", nullable=true)
     */
    protected $stove_type;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\Range(min="0.01", minMessage = "Nie za mało?")
     */
    protected $stove_power;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Email
     */
    protected $email;

    /**
     * @ORM\ManyToOne(targetEntity="House", inversedBy="calculations",cascade={"all"})
     */
    protected $house;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    public static function create()
    {
        $calc = new Calculation();
        $calc->setLatitude(51.917168);
        $calc->setLongitude(19.138184);

        return $calc;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->walls = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getFuelLabel()
    {
        $labels = array(
          'coal' => 'Węgiel kamienny',
          'coke' => 'Koks',
          'sand_coal' => 'Miał węglowy',
          'brown_coal' => 'Węgiel brunatny',
          'wood' => 'Drewno',
          'pellet' => 'Pellet/brykiety',
          'gas_e' => 'Gaz ziemny typ E (GZ-50)',
          'gas_ls' => 'Gaz ziemny typ Ls (GZ-35)',
          'gas_lw' => 'Gaz ziemny typ Lw (GZ-41,5)',
          'electricity' => 'Prąd elektryczny',
        );

        $amount = round($this->getFuelConsumption(), 1);

        if (stristr($this->getFuelType(), 'coal') || $this->getFuelType() == 'pellet' || $this->getFuelType() == 'coke') {
            $amount .= 't';
        } elseif (stristr($this->getFuelType(), 'gas')) {
            $amount .= 'm3';
        } elseif ($this->getFuelType() == 'wood') {
            $amount .= 'mp';
        } else {
            $amount .= 'kWh';
        }

        return $labels[$this->getFuelType()].', '.$amount;
    }

    public function getLabel()
    {
        $types = array(
            'single_house' => 'Budynek jednorodzinny',
            'double_house' => 'Bliźniak',
            'row_house' => 'Dom w zabudowie szeregowej',
            'apartment' => 'Mieszkanie',
        );

        $house = $this->getHouse();
        $l = $house->getBuildingLength();
        $w = $house->getBuildingWidth();
        $floors = $house->getNumberFloors();

        return $types[$this->building_type] . ', ' . round($w * $l * $floors) . 'm2';
    }

    public function getSlug()
    {
        return base_convert($this->getId(), 10, 36);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set construction_year
     *
     * @param  integer     $constructionYear
     * @return Calculation
     */
    public function setConstructionYear($constructionYear)
    {
        $this->construction_year = $constructionYear;

        return $this;
    }

    /**
     * Get construction_year
     *
     * @return integer
     */
    public function getConstructionYear()
    {
        return $this->construction_year;
    }

    /**
     * Set latitude
     *
     * @param  float       $latitude
     * @return Calculation
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param  float       $longitude
     * @return Calculation
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set number_floors
     *
     * @param  integer     $numberFloors
     * @return Calculation
     */
    public function setNumberFloors($numberFloors)
    {
        $this->number_floors = $numberFloors;

        return $this;
    }

    /**
     * Get number_floors
     *
     * @return integer
     */
    public function getNumberFloors()
    {
        return $this->number_floors;
    }

    /**
     * Set roof_type
     *
     * @param  string      $roofType
     * @return Calculation
     */
    public function setRoofType($roofType)
    {
        $this->roof_type = $roofType;

        return $this;
    }

    /**
     * Get roof_type
     *
     * @return string
     */
    public function getRoofType()
    {
        return $this->roof_type;
    }

    /**
     * Set number_doors
     *
     * @param  integer     $numberDoors
     * @return Calculation
     */
    public function setNumberDoors($numberDoors)
    {
        $this->number_doors = $numberDoors;

        return $this;
    }

    /**
     * Get number_doors
     *
     * @return integer
     */
    public function getNumberDoors()
    {
        return $this->number_doors;
    }

    /**
     * Set number_windows
     *
     * @param  integer     $numberWindows
     * @return Calculation
     */
    public function setNumberWindows($numberWindows)
    {
        $this->number_windows = $numberWindows;

        return $this;
    }

    /**
     * Get number_windows
     *
     * @return integer
     */
    public function getNumberWindows()
    {
        return $this->number_windows;
    }

    /**
     * Set doors_type
     *
     * @param  string      $doorsType
     * @return Calculation
     */
    public function setDoorsType($doorsType)
    {
        $this->doors_type = $doorsType;

        return $this;
    }

    /**
     * Get doors_type
     *
     * @return string
     */
    public function getDoorsType()
    {
        return $this->doors_type;
    }

    /**
     * Set windows_type
     *
     * @param  string      $windowsType
     * @return Calculation
     */
    public function setWindowsType($windowsType)
    {
        $this->windows_type = $windowsType;

        return $this;
    }

    /**
     * Get windows_type
     *
     * @return string
     */
    public function getWindowsType()
    {
        return $this->windows_type;
    }

    /**
     * Set has_basement
     *
     * @param  boolean     $hasBasement
     * @return Calculation
     */
    public function setHasBasement($hasBasement)
    {
        $this->has_basement = $hasBasement;

        return $this;
    }

    /**
     * Get has_basement
     *
     * @return boolean
     */
    public function getHasBasement()
    {
        return $this->has_basement;
    }

    /**
     * Set ventilation_type
     *
     * @param  string      $ventilationType
     * @return Calculation
     */
    public function setVentilationType($ventilationType)
    {
        $this->ventilation_type = $ventilationType;

        return $this;
    }

    /**
     * Get ventilation_type
     *
     * @return string
     */
    public function getVentilationType()
    {
        return $this->ventilation_type;
    }

    /**
     * Set is_attic_heated
     *
     * @param  boolean     $isAtticHeated
     * @return Calculation
     */
    public function setIsAtticHeated($isAtticHeated)
    {
        $this->is_attic_heated = $isAtticHeated;

        return $this;
    }

    /**
     * Get is_attic_heated
     *
     * @return boolean
     */
    public function getIsAtticHeated()
    {
        return $this->is_attic_heated;
    }

    /**
     * Set is_basement_heated
     *
     * @param  boolean     $isBasementHeated
     * @return Calculation
     */
    public function setIsBasementHeated($isBasementHeated)
    {
        $this->is_basement_heated = $isBasementHeated;

        return $this;
    }

    /**
     * Get is_basement_heated
     *
     * @return boolean
     */
    public function getIsBasementHeated()
    {
        return $this->is_basement_heated;
    }

    /**
     * Set ceiling_isolation_layer
     *
     * @param  \Kraken\WarmBundle\Entity\Layer $ceilingIsolationLayer
     * @return Calculation
     */
    public function setCeilingIsolationLayer(\Kraken\WarmBundle\Entity\Layer $ceilingIsolationLayer = null)
    {
        $this->ceiling_isolation_layer = $ceilingIsolationLayer;

        return $this;
    }

    /**
     * Get ceiling_isolation_layer
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getCeilingIsolationLayer()
    {
        return $this->ceiling_isolation_layer;
    }

    /**
     * Set roof_isolation_layer
     *
     * @param  \Kraken\WarmBundle\Entity\Layer $roofIsolationLayer
     * @return Calculation
     */
    public function setRoofIsolationLayer(\Kraken\WarmBundle\Entity\Layer $roofIsolationLayer = null)
    {
        $this->roof_isolation_layer = $roofIsolationLayer;

        return $this;
    }

    /**
     * Get roof_isolation_layer
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getRoofIsolationLayer()
    {
        return $this->roof_isolation_layer;
    }

    /**
     * Set ground_floor_isolation_layer
     *
     * @param  \Kraken\WarmBundle\Entity\Layer $groundFloorIsolationLayer
     * @return Calculation
     */
    public function setGroundFloorIsolationLayer(\Kraken\WarmBundle\Entity\Layer $groundFloorIsolationLayer = null)
    {
        $this->ground_floor_isolation_layer = $groundFloorIsolationLayer;

        return $this;
    }

    /**
     * Get ground_floor_isolation_layer
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getGroundFloorIsolationLayer()
    {
        return $this->ground_floor_isolation_layer;
    }

    /**
     * Set basement_floor_isolation_layer
     *
     * @param  \Kraken\WarmBundle\Entity\Layer $basementFloorIsolationLayer
     * @return Calculation
     */
    public function setBasementFloorIsolationLayer(\Kraken\WarmBundle\Entity\Layer $basementFloorIsolationLayer = null)
    {
        $this->basement_floor_isolation_layer = $basementFloorIsolationLayer;

        return $this;
    }

    /**
     * Get basement_floor_isolation_layer
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getBasementFloorIsolationLayer()
    {
        return $this->basement_floor_isolation_layer;
    }

    /**
     * Set highest_ceiling_isolation_layer
     *
     * @param  \Kraken\WarmBundle\Entity\Layer $highestCeilingIsolationLayer
     * @return Calculation
     */
    public function setHighestCeilingIsolationLayer(\Kraken\WarmBundle\Entity\Layer $highestCeilingIsolationLayer = null)
    {
        $this->highest_ceiling_isolation_layer = $highestCeilingIsolationLayer;

        return $this;
    }

    /**
     * Get highest_ceiling_isolation_layer
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getHighestCeilingIsolationLayer()
    {
        return $this->highest_ceiling_isolation_layer;
    }

    /**
     * Set lowest_ceiling_isolation_layer
     *
     * @param  \Kraken\WarmBundle\Entity\Layer $lowestCeilingIsolationLayer
     * @return Calculation
     */
    public function setLowestCeilingIsolationLayer(\Kraken\WarmBundle\Entity\Layer $lowestCeilingIsolationLayer = null)
    {
        $this->lowest_ceiling_isolation_layer = $lowestCeilingIsolationLayer;

        return $this;
    }

    /**
     * Get lowest_ceiling_isolation_layer
     *
     * @return \Kraken\WarmBundle\Entity\Layer
     */
    public function getLowestCeilingIsolationLayer()
    {
        return $this->lowest_ceiling_isolation_layer;
    }

    /**
     * Set has_garage
     *
     * @param  boolean     $hasGarage
     * @return Calculation
     */
    public function setHasGarage($hasGarage)
    {
        $this->has_garage = $hasGarage;

        return $this;
    }

    /**
     * Get has_garage
     *
     * @return boolean
     */
    public function getHasGarage()
    {
        return $this->has_garage;
    }

    /**
     * Set created
     *
     * @param  \DateTime   $created
     * @return Calculation
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param  \DateTime   $updated
     * @return Calculation
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set building_type
     *
     * @param  string      $buildingType
     * @return Calculation
     */
    public function setBuildingType($buildingType)
    {
        $this->building_type = $buildingType;

        return $this;
    }

    /**
     * Get building_type
     *
     * @return string
     */
    public function getBuildingType()
    {
        return $this->building_type;
    }

    /**
     * Set indoor_temperature
     *
     * @param  float       $indoorTemperature
     * @return Calculation
     */
    public function setIndoorTemperature($indoorTemperature)
    {
        $this->indoor_temperature = $indoorTemperature;

        return $this;
    }

    /**
     * Get indoor_temperature
     *
     * @return float
     */
    public function getIndoorTemperature()
    {
        return $this->indoor_temperature;
    }

    /**
     * Set fuel_type
     *
     * @param  string      $fuelType
     * @return Calculation
     */
    public function setFuelType($fuelType)
    {
        $this->fuel_type = $fuelType;

        return $this;
    }

    /**
     * Get fuel_type
     *
     * @return string
     */
    public function getFuelType()
    {
        return $this->fuel_type;
    }

    public function setStoveType($stoveType)
    {
        $this->stove_type = $stoveType;

        return $this;
    }

    /**
     * Get stove_type
     *
     * @return string
     */
    public function getStoveType()
    {
        return $this->stove_type;
    }

    /**
     * Set fuel_consumption
     *
     * @param  float       $fuelConsumption
     * @return Calculation
     */
    public function setFuelConsumption($fuelConsumption)
    {
        $this->fuel_consumption = $fuelConsumption;

        return $this;
    }

    /**
     * Get fuel_consumption
     *
     * @return float
     */
    public function getFuelConsumption()
    {
        return $this->fuel_consumption;
    }

    /**
     * Set fuel_cost
     *
     * @param  float       $fuelCost
     * @return Calculation
     */
    public function setFuelCost($fuelCost)
    {
        $this->fuel_cost = $fuelCost;

        return $this;
    }

    /**
     * Get fuel_cost
     *
     * @return float
     */
    public function getFuelCost()
    {
        return $this->fuel_cost;
    }

    /**
     * Set house
     *
     * @param  \Kraken\WarmBundle\Entity\House $house
     * @return Calculation
     */
    public function setHouse(\Kraken\WarmBundle\Entity\House $house = null)
    {
        $this->house = $house;

        return $this;
    }

    /**
     * Get house
     *
     * @return \Kraken\WarmBundle\Entity\House
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * Set stove_power
     *
     * @param  float       $stovePower
     * @return Calculation
     */
    public function setStovePower($stovePower)
    {
        $this->stove_power = $stovePower;

        return $this;
    }

    /**
     * Get stove_power
     *
     * @return float
     */
    public function getStovePower()
    {
        return $this->stove_power;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function isUsingSolidFuel()
    {
        return !stristr($this->getFuelType(), 'electricity') && !stristr($this->getFuelType(), 'gas');
    }
}
