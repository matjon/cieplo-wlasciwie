<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @ORM\Entity
 * @ORM\Table(name="house")
 * @Assert\Callback(methods={"areIsolationLayersValid", "isNumberFloorsValid"})
 */
class House
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     * @Assert\NotBlank
     * @Assert\Range(min="1", minMessage="Porządny dom powinien mieć min. 1m szerokości", max="100", maxMessage = "Powyżej 100m szerokości to już hangar, a nie dom mieszkalny")
     */
    protected $building_length;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     * @Assert\NotBlank
     * @Assert\Range(min="1", minMessage = "Porządny dom powinien mieć min. 1m długości", max="100", maxMessage = "Powyżej 100m długości to już hangar, a nie dom mieszkalny")
     */
    protected $building_width;

    /**
     * @ORM\Column(type="integer", length=2)
     * @Assert\NotBlank
     * @Assert\Range(min="1", minMessage = "Nawet ziemianka ma min. 1 piętro", max="99", maxMessage = "Nie wierzę, że masz więcej niż 100 pięter w swojej chałupie")
     */
    protected $number_floors;

    /**
     * @ORM\Column(type="integer", length=2)
     * @Assert\NotBlank
     * @Assert\Range(min="1", minMessage = "Nawet ziemianka ma min. 1 piętro ogrzewane", max="99", maxMessage = "Za mało mam palców by to policzyć")
     */
    protected $number_heated_floors;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $whats_unheated;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     * @Assert\Range(min="0", minMessage = "Za mało drzwi zewnętrznych", max="99", maxMessage = "Więcej jak 99 drzwi nie jest ci potrzebne. Sprzedaj połowę.")
     */
    protected $number_doors;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('old_wooden', 'old_metal', 'new_wooden', 'new_metal', 'other')")
     */
    protected $doors_type;

    /**
     * @ORM\Column(type="integer", length=2)
     * @Assert\NotBlank
     * @Assert\Range(min="1", minMessage = "Min. 1 okno powinieneś posiadać", max="99", maxMessage = "Więcej jak 99 okien nie jest ci potrzebne. Sprzedaj połowę.")
     */
    protected $number_windows;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('old_single_glass', 'old_double_glass', 'old_improved', 'semi_new_double_glass', 'new_double_glass', 'new_triple_glass')")
     */
    protected $windows_type;

    /**
     * @ORM\OneToMany(targetEntity="Wall", mappedBy="house", cascade={"all"})
     * @Assert\Valid
     */
    protected $walls;

    /**
     * @ORM\Column(type="string",columnDefinition="ENUM('flat', 'oblique', 'steep')")
     */
    protected $roof_type;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="highest_ceiling_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $highest_ceiling_isolation_layer;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="roof_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $roof_isolation_layer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $has_balcony;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $has_basement;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="ground_floor_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $ground_floor_isolation_layer;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="basement_floor_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $basement_floor_isolation_layer;

    /**
     * @ORM\ManyToOne(targetEntity="Layer", cascade={"all"})
     * @ORM\JoinColumn(name="lowest_ceiling_isolation_layer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $lowest_ceiling_isolation_layer;

    /**
     * @ORM\Column(type="string",columnDefinition="ENUM('natural', 'mechanical', 'mechanical_recovery')")
     */
    protected $ventilation_type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $has_garage;

    /**
     * @ORM\OneToMany(targetEntity="Calculation", mappedBy="house", cascade={"all"})
     */
    protected $calculations;

    /**
     * @ORM\ManyToOne(targetEntity="Apartment", cascade={"all"}, inversedBy="houses")
     * @ORM\JoinColumn(name="apartment_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $apartment;

    public function areIsolationLayersValid(ExecutionContext $context)
    {
        if ($this->highest_ceiling_isolation_layer) {
            if ($this->highest_ceiling_isolation_layer->getSize() && !$this->highest_ceiling_isolation_layer->getMaterial()) {
                $context->addViolationAtSubPath('highest_ceiling_isolation_layer', 'Wybierz materiał warstwy izolacji dachu', array(), null);
            }
            if (!$this->highest_ceiling_isolation_layer->getSize() && $this->highest_ceiling_isolation_layer->getMaterial()) {
                $context->addViolationAtSubPath('highest_ceiling_isolation_layer', 'Wybierz grubość warstwy izolacji dachu', array(), null);
            }
        }

        if ($this->lowest_ceiling_isolation_layer) {
            if ($this->lowest_ceiling_isolation_layer->getSize() && !$this->lowest_ceiling_isolation_layer->getMaterial()) {
                $context->addViolationAtSubPath('lowest_ceiling_isolation_layer', 'Wybierz materiał warstwy izolacji stropu nad parterem', array(), null);
            }
            if (!$this->lowest_ceiling_isolation_layer->getSize() && $this->lowest_ceiling_isolation_layer->getMaterial()) {
                $context->addViolationAtSubPath('lowest_ceiling_isolation_layer', 'Wybierz grubość warstwy izolacji stropu nad parterem', array(), null);
            }
        }

        if ($this->basement_floor_isolation_layer) {
            if ($this->basement_floor_isolation_layer->getSize() && !$this->basement_floor_isolation_layer->getMaterial()) {
                $context->addViolationAtSubPath('basement_floor_isolation_layer', 'Wybierz materiał warstwy izolacji podłogi piwnicy', array(), null);
            }
            if (!$this->basement_floor_isolation_layer->getSize() && $this->basement_floor_isolation_layer->getMaterial()) {
                $context->addViolationAtSubPath('basement_floor_isolation_layer', 'Wybierz grubość warstwy izolacji podłogi piwnicy', array(), null);
            }
        }

        if ($this->ground_floor_isolation_layer) {
            if ($this->ground_floor_isolation_layer->getSize() && !$this->ground_floor_isolation_layer->getMaterial()) {
                $context->addViolationAtSubPath('ground_floor_isolation_layer', 'Wybierz materiał warstwy izolacji podłogi parteru', array(), null);
            }
            if (!$this->ground_floor_isolation_layer->getSize() && $this->ground_floor_isolation_layer->getMaterial()) {
                $context->addViolationAtSubPath('ground_floor_isolation_layer', 'Wybierz grubość warstwy izolacji podłogi parteru', array(), null);
            }
        }

        if ($this->roof_isolation_layer) {
            if ($this->roof_isolation_layer->getSize() && !$this->roof_isolation_layer->getMaterial()) {
                $context->addViolationAtSubPath('roof_isolation_layer', 'Wybierz materiał warstwy izolacji dachu', array(), null);
            }
            if (!$this->roof_isolation_layer->getSize() && $this->roof_isolation_layer->getMaterial()) {
                $context->addViolationAtSubPath('roof_isolation_layer', 'Wybierz grubość warstwy izolacji dachu', array(), null);
            }
        }
    }

    public function isNumberFloorsValid(ExecutionContext $context)
    {
        if ($this->number_floors < $this->number_heated_floors) {
            $context->addViolationAt('number_heated_floors', 'Nie możesz ogrzewać więcej pięter niż ma twój dom', array(), null);
        }
    }

    public static function create()
    {
        $house = new House();
        $wall = new Wall();
        $house->addWall($wall);

        return $house;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->walls = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set building_length
     *
     * @param  integer $buildingLength
     * @return House
     */
    public function setBuildingLength($buildingLength)
    {
        $this->building_length = $buildingLength;

        return $this;
    }

    /**
     * Get building_length
     *
     * @return integer
     */
    public function getBuildingLength()
    {
        return $this->building_length;
    }

    /**
     * Set building_width
     *
     * @param  integer $buildingWidth
     * @return House
     */
    public function setBuildingWidth($buildingWidth)
    {
        $this->building_width = $buildingWidth;

        return $this;
    }

    /**
     * Get building_width
     *
     * @return integer
     */
    public function getBuildingWidth()
    {
        return $this->building_width;
    }

    /**
     * Set number_floors
     *
     * @param  integer $numberFloors
     * @return House
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
     * Set number_doors
     *
     * @param  integer $numberDoors
     * @return House
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
     * Set doors_type
     *
     * @param  string $doorsType
     * @return House
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
     * Set number_windows
     *
     * @param  integer $numberWindows
     * @return House
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
     * Set windows_type
     *
     * @param  string $windowsType
     * @return House
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
     * Set roof_type
     *
     * @param  string $roofType
     * @return House
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
     * Set has_basement
     *
     * @param  boolean $hasBasement
     * @return House
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
     * @param  string $ventilationType
     * @return House
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
     * Set has_garage
     *
     * @param  boolean $hasGarage
     * @return House
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
     * Add walls
     *
     * @param  \Kraken\WarmBundle\Entity\Wall $walls
     * @return House
     */
    public function addWall(\Kraken\WarmBundle\Entity\Wall $walls)
    {
        $this->walls[] = $walls;

        return $this;
    }

    /**
     * Remove walls
     *
     * @param \Kraken\WarmBundle\Entity\Wall $walls
     */
    public function removeWall(\Kraken\WarmBundle\Entity\Wall $walls)
    {
        $this->walls->removeElement($walls);
    }

    /**
     * Get walls
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWalls()
    {
        return $this->walls;
    }

    /**
     * Set highest_ceiling_isolation_layer
     *
     * @param  \Kraken\WarmBundle\Entity\Layer $highestCeilingIsolationLayer
     * @return House
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
     * Set roof_isolation_layer
     *
     * @param  \Kraken\WarmBundle\Entity\Layer $roofIsolationLayer
     * @return House
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
     * @return House
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
     * @return House
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
     * Set lowest_ceiling_isolation_layer
     *
     * @param  \Kraken\WarmBundle\Entity\Layer $lowestCeilingIsolationLayer
     * @return House
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
     * Add calculations
     *
     * @param  \Kraken\WarmBundle\Entity\Calculation $calculations
     * @return House
     */
    public function addCalculation(\Kraken\WarmBundle\Entity\Calculation $calculations)
    {
        $this->calculations[] = $calculations;

        return $this;
    }

    /**
     * Remove calculations
     *
     * @param \Kraken\WarmBundle\Entity\Calculation $calculations
     */
    public function removeCalculation(\Kraken\WarmBundle\Entity\Calculation $calculations)
    {
        $this->calculations->removeElement($calculations);
    }

    /**
     * Get calculations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCalculations()
    {
        return $this->calculations;
    }

    public function getCalculation()
    {
        return $this->calculations->first();
    }

    /**
     * Set has_balcony
     *
     * @param  boolean $hasBalcony
     * @return House
     */
    public function setHasBalcony($hasBalcony)
    {
        $this->has_balcony = $hasBalcony;

        return $this;
    }

    /**
     * Get has_balcony
     *
     * @return boolean
     */
    public function getHasBalcony()
    {
        return $this->has_balcony;
    }

    /**
     * Set apartment
     *
     * @param  \Kraken\WarmBundle\Entity\Apartment $apartment
     * @return House
     */
    public function setApartment(\Kraken\WarmBundle\Entity\Apartment $apartment = null)
    {
        $this->apartment = $apartment;

        return $this;
    }

    /**
     * Get apartment
     *
     * @return \Kraken\WarmBundle\Entity\Apartment
     */
    public function getApartment()
    {
        return $this->apartment;
    }

    /**
     * Set number_heated_floors
     *
     * @param  integer $numberHeatedFloors
     * @return House
     */
    public function setNumberHeatedFloors($numberHeatedFloors)
    {
        $this->number_heated_floors = $numberHeatedFloors;

        return $this;
    }

    /**
     * Get number_heated_floors
     *
     * @return integer
     */
    public function getNumberHeatedFloors()
    {
        return $this->number_heated_floors;
    }

    /**
     * Set whats_unheated
     *
     * @param  string $whatsUnheated
     * @return House
     */
    public function setWhatsUnheated($whatsUnheated)
    {
        $this->whats_unheated = $whatsUnheated;

        return $this;
    }

    /**
     * Get whats_unheated
     *
     * @return string
     */
    public function getWhatsUnheated()
    {
        return $this->whats_unheated;
    }
}
