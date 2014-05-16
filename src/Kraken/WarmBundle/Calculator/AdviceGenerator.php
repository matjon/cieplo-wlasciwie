<?php

namespace Kraken\WarmBundle\Calculator;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Service\InstanceService;

class AdviceGenerator
{
    protected $building;
    protected $calculator;
    protected $instance;

    public function __construct(InstanceService $instance, BuildingInterface $building, EnergyCalculator $calculator)
    {
        $this->building = $building;
        $this->calculator = $calculator;
        $this->instance = $instance->get();
    }

    public function getAdviceFor(Calculation $calc)
    {
        $advice = array();

        $heatingPower = $this->calculator->getMaxHeatingPower();

        try {
            $fuelType = $this->instance->getFuelType();
            $usingSolidFuel = stripos($fuelType, 'gas') === false && $fuelType != 'electricity';
            $stoveEfficiency = $this->calculator->getYearlyStoveEfficiency();

            if ($stoveEfficiency < 0.4) {
                if ($usingSolidFuel) {
                    $piece = 'Aktualnie większość pieniędzy wyrzucasz w atmosferę. ';
                    if (in_array($this->instance->getStoveType(), array('', 'manual_upward'))) {
                        $piece .= '<a href="http://czysteogrzewanie.pl/jak-palic-w-piecu" target="_blank">Wypróbuj palenie od góry</a> - będzie taniej i wygodniej.';
                    }
                    $advice['Naucz się palić albo kup kocioł podajnikowy'] = $piece;
                }
            }

            if ($usingSolidFuel) {
                if ($stoveEfficiency > 0.6 && $stoveEfficiency < 0.9) {
                    $advice['Nie kupuj nowego kotła zasypowego'] = 'Obecny pracuje ze znakomitą sprawnością, a nowy kocioł zasypowy, zwłaszcza tani ulep z dmuchawą, może znacznie pogorszyć sytuację.';
                }
            } else {
                if ($stoveEfficiency > 0.85 && $stoveEfficiency < 1.2) {
                    $advice['Nie majstruj nic w ogrzewaniu, jeśli nie musisz'] = 'Obecna instalacja pracuje ze znakomitą sprawnością.';
                }
            }

            $stoveOversized = $this->calculator->isStoveOversized();

            if ($stoveOversized && $stoveEfficiency < 0.7 && $heatingPower > 10000) {
                $advice['Kup kocioł o mniejszej mocy'] = 'Obecny ma o wiele za dużą moc, przez co pożera bezproduktywnie nawet 2/3 opału.'.
                    ' Możesz też <a href="http://czysteogrzewanie.pl/zakupy/mocy-przybywaj-dobor-mocy-kotla-weglowego/#Co_zrobi_z_przewymiarowanym_kotem" target="_blank">rozwiązać problem tanio.</a>';
            }

        } catch (\Exception $e) {}

        if ($heatingPower <= 8000) {
            $advice['Szkoda życia na szuflowanie węgla'] = "Twój dom jest energooszczędny, zostaw węgiel w spokoju! Za ledwo kilkaset złotych więcej w skali roku możesz ogrzewać gazem ziemnym.";
        }

        $breakdown = $this->building->getEnergyLossBreakdown();
        $keys = array_keys($breakdown);
        $label = strip_tags($keys[count($keys)-1]);

        //TODO nie proponować ocieplenia gdy ocieplenie już jest
        if ($label != 'Wentylacja' && $heatingPower > 8000) {
            $advice[$label . ' to główne źródło strat ciepła'] = 'Rozważ dodatkowe ocieplenie - koszt zwróci się błyskawicznie.';
        }

        if (empty($advice)) {
            $advice['Jest dobrze'] = 'Twój dom jest energooszczędny, a ogrzewanie ekonomiczne. Idź i zajmij się życiem.';
        }

        return $advice;

    }
}
