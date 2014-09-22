<?php

namespace Kraken\WarmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HouseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('building_length', null, array(
                'label' => 'Długość budynku',
                'attr'  => array(
                    'input_group' => array(
                        'append'  => 'm'
                    )
                ),
            ))
            ->add('building_width', null, array(
                'label' => 'Szerokość budynku',
                'attr'  => array(
                    'input_group' => array(
                        'append'  => 'm'
                    )
                ),
            ))
            ->add('floor_height', 'choice', array(
                'choices' => array(
                    '2.3' => 'Niskie (poniżej 2,5m)',
                    '2.6' => 'Standardowe (ok. 2,6m)',
                    '3.0' => 'Wysokie (3m i więcej)',
                ),
                'label' => 'Wysokość piętra',
                'required' => true,
            ))
            ->add('number_floors', null, array(
                'label' => 'Liczba wszystkich pięter budynku',
                'attr' => array(
                    'help_text' => 'Policz zarówno te zamieszkałe i ogrzewane jak i nieogrzewaną piwnicę czy strych',
                )
            ))
            ->add('number_heated_floors', null, array(
                'label' => 'Liczba pięter ogrzewanych',
            ))
            ->add('whats_unheated', 'choice', array(
                'choices' => array(
                    'basement' => 'Piwnica',
                    'ground_floor' => 'Parter',
                    'floor' => 'Jedno z pięter',
                    'attic' => 'Poddasze',
                ),
                'label' => 'Które z pięter jest nieogrzewane?',
                'required' => false
            ))
            ->add('has_balcony', null, array(
                'required' => false,
                'label' => 'Dom posiada balkon(y)'
            ))
            ->add('construction_type', 'choice', array(
                'choices' => array(
                    'traditional' => 'Tradycyjna - murowana lub drewniana',
                    'canadian' => 'Dom kanadyjski, szkieletowy',
                ),
                'label' => 'Typ konstrukcji',
                'required' => true,
            ))
            ->add('walls','collection', array(
                'type' =>  new WallType(),
                'allow_add' => false,
                'prototype' => false,
            ))
            ->add('number_doors', null, array(
                'label' => 'Liczba drzwi zewnętrznych',
                'attr' => array(
                    'help_text' => 'W części ogrzewanej',
                )
            ))
            ->add('doors_type', 'choice', array(
                'choices' => array(
                    'old_wooden' => 'Stare drewniane',
                    'old_metal' => 'Stare metalowe',
                    'new_wooden' => 'Nowe drewniane',
                    'new_metal' => 'Nowe metalowe',
                    'other' => 'Inne',
                ),
                'label' => 'Rodzaj drzwi zewnętrznych',
                'attr' => array(
                    'help_text' => 'Jeśli masz starsze i nowsze, wybierz te, których jest najwięcej',
                )
            ))
            ->add('number_windows', null, array(
                'label' => 'Liczba okien',
                'attr' => array(
                    'help_text' => 'W części ogrzewanej',
                )
            ))
            ->add('windows_type', 'choice', array(
                'choices' => array(
                    'old_single_glass' => 'Stare z pojedynczą szybą',
                    'old_double_glass' => 'Stare z min. dwiema szybami',
                    'old_improved' => 'Stare, ale doszczelnione',
                    'semi_new_double_glass' => 'Starsze niż 10-letnie z szybami zespolonymi',
                    'new_double_glass' => 'Nowe z szybami zespolonymi',
                    'new_triple_glass' => 'Nowe z trzema szybami',
                ),
                'label' => 'Rodzaj okien'
            ))
            ->add('roof_type', 'choice', array(
                'choices' => array(
                    'flat' => 'płaski',
                    'oblique' => 'dwuspadowy o niewielkim nachyleniu',
                    'steep' => 'dwuspadowy stromy (rejony górskie)',
                ),
                'label' => 'Rodzaj dachu'
            ))
            ->add('highest_ceiling_isolation_layer', new LayerType(), array(
                'label' => 'Izolacja najwyższego stropu',
                'material_type' => 'for_ceiling',
                'required' => false,
            ))
            ->add('roof_isolation_layer', new LayerType(), array(
                'material_type' => 'for_ceiling',
                'label' => 'Izolacja dachu',
                'required' => false,
            ))
            ->add('has_basement', null, array(
                'label' => 'Dom jest podpiwniczony',
                'required' => false,
            ))
            ->add('ground_floor_isolation_layer', new LayerType(), array(
                'label' => 'Izolacja podłogi parteru',
                'material_type' => 'for_floor',
                'required' => false,
            ))
            ->add('basement_floor_isolation_layer', new LayerType(), array(
                'label' => 'Izolacja podłogi w piwnicy',
                'material_type' => 'for_floor',
                'required' => false
            ))
            ->add('lowest_ceiling_isolation_layer', new LayerType(), array(
                'label' => 'Izolacja stropu nad parterem',
                'material_type' => 'for_ceiling',
                'required' => false,
            ))
            ->add('ventilation_type', 'choice', array(
                'choices' => array(
                    'natural' => 'Naturalna lub grawitacyjna',
                    'mechanical' => 'Mechaniczna',
                    'mechanical_recovery' => 'Mechaniczna z odzyskiem ciepła',
                ),
                'label' => 'Rodzaj wentylacji'
            ))
            ->add('has_garage', null, array(
                'label' => 'Dom posiada garaż',
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\House',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'calculation';
    }
}
