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
        // in case someone put amount in kgs, not in tons
        if ((stristr($this->getFuelType(), 'coal') || $this->getFuelType() == 'pellet' || $this->getFuelType() == 'coke') && $this->fuel_consumption >= 1000) {
            return $this->fuel_consumption/1000;
        }

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
