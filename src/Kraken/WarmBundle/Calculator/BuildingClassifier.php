<?php

namespace Kraken\WarmBundle\Calculator;

class BuildingClassifier
{
    const CLASS_A_PLUS = "A+";
    const CLASS_A = "A";
    const CLASS_B = "B";
    const CLASS_C = "C";
    const CLASS_D = "D";
    const CLASS_E = "E";
    const CLASS_F = "F";

    protected $strings = array(
        self::CLASS_A_PLUS => 'a-plus',
        self::CLASS_A => 'a',
        self::CLASS_B => 'b',
        self::CLASS_C => 'c',
        self::CLASS_D => 'd',
        self::CLASS_E => 'e',
        self::CLASS_F => 'f',
    );

    protected $labels = array(
        self::CLASS_A_PLUS => 'Dom pasywny',
        self::CLASS_A => 'Dom niskoenergetyczny',
        self::CLASS_B => 'Dom energooszczędny',
        self::CLASS_C => 'Dom średnio energooszczędny',
        self::CLASS_D => 'Dom średnio energochłonny',
        self::CLASS_E => 'Dom energochłonny',
        self::CLASS_F => 'Dom wysoko energochłonny',
    );

    protected $calculator;

    public function __construct(EnergyCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function getClassString()
    {
        return $this->strings[$this->getClass()];
    }

    public function getClassLabel()
    {
        return $this->labels[$this->getClass()];
    }

    public function getClass()
    {
        $factor = $this->calculator->getYearlyEnergyConsumptionFactor();

        $class = self::CLASS_F;

        if ($factor <= 20) {
            $class = self::CLASS_A_PLUS;
        } elseif ($factor <= 45) {
            $class = self::CLASS_A;
        } elseif ($factor < 80) {
            $class = self::CLASS_B;
        } elseif ($factor < 100) {
            $class = self::CLASS_C;
        } elseif ($factor < 150) {
            $class = self::CLASS_D;
        } elseif ($factor < 250) {
            $class = self::CLASS_E;
        }

        return $class;
    }
}
