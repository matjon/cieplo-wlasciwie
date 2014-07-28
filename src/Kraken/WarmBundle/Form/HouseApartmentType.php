<?php

namespace Kraken\WarmBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HouseApartmentType extends HouseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('roof_type')
            ->remove('construction_type')
            ->remove('roof_isolation_layer')
            ->remove('is_attic_heated')
            ->remove('has_basement')
            ->remove('is_basement_heated')
            ->remove('is_ground_floor_heated')
            ->remove('ground_floor_isolation_layer')
            ->remove('lowest_ceiling_isolation_layer')
            ->remove('basement_floor_isolation_layer')
            ->remove('has_garage')
            ->remove('has_balcony')
            ->remove('building_length')
            ->remove('building_width')
            ->remove('number_floors')
            ->add('apartment', new ApartmentType(), array(
                'required' => false,
            ))
            ->add('has_balcony', null, array(
                'required' => false,
                'label' => 'Mieszkanie posiada balkon(y)'
            ))
            ->add('building_length', null, array(
                'label' => 'Długość mieszkania',
                'attr'  => array(
                    'input_group' => array(
                        'append'  => 'm'
                    )
                ),
            ))
            ->add('building_width', null, array(
                'label' => 'Szerokość mieszkania',
                'attr'  => array(
                    'input_group' => array(
                        'append'  => 'm'
                    )
                ),
            ))
            ->add('number_floors', null, array(
                'label' => 'Liczba pięter mieszkania'
            ))
            ->add('lowest_ceiling_isolation_layer', new LayerType(), array(
                'label' => 'Izolacja podłogi',
                'material_type' => 'for_ceiling',
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
