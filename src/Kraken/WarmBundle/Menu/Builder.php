<?php

namespace Kraken\WarmBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function navbar(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Start', array('route' => 'start'));
        $menu->addChild('Co to jest', array('route' => 'what'));
        $menu->addChild('Jak to działa', array('route' => 'how_it_works'));
        $menu->addChild('Czemu nie działa', array('route' => 'why_not_works'));
        $menu->addChild('Zasady', array('route' => 'rules'));
        $menu->addChild('Moje wyniki', array('route' => 'my_results'));
        $menu->addChild('Kontakt', array('uri' => 'http://czysteogrzewanie.pl/kontakt'));

        return $menu;
    }

    public function resultTabs(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $item = $menu->addChild('Informacje o budynku', array('uri' => '#info'));
        $item = $menu->addChild('Koszty i efektywność ogrzewania', array('uri' => '#koszty'));
        $item = $menu->addChild('Jak żyć', array('uri' => '#rady'));

        return $menu;
    }
}
