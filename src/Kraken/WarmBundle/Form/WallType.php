<?php

namespace Kraken\WarmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WallType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('has_another_layer', 'checkbox', array(
                'label' => 'Ściana ma drugą warstwę z innego materiału',
                'mapped' => false
            ))
            ->add('has_isolation_inside', 'checkbox', array(
                'label' => 'Ściana ma izolację w środku',
                'mapped' => false
            ))
            ->add('has_isolation_outside', 'checkbox', array(
                'label' => 'Dom jest docieplony',
                'mapped' => false
            ))
            ->add('construction_layer', new LayerType(), array(
                'material_type' => 'for_wall_construction_layer',
                'required' => true,
            ))
            ->add('isolation_layer', new LayerType(), array(
                'material_type' => 'for_wall_internal_layer',
                'required' => false,
            ))
            ->add('outside_layer', new LayerType(), array(
                'material_type' => 'for_wall_facade_layer',
                'required' => false,
            ))
            ->add('extra_isolation_layer', new LayerType(), array(
                'material_type' => 'for_wall_isolation_layer',
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\Wall',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'wall';
    }
}
