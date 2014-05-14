<?php

namespace Kraken\WarmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('number_external_walls', null, array(
                'label' => 'Liczba ścian zewnętrznych',
                'attr' => array(
                    'help' => 'Chodzi o te ściany twojego mieszkania, za którymi jest dwór/pole, a nie inne pomieszczenia',
                )
            ))
            ->add('number_unheated_walls', null, array(
                'label' => 'Liczba ścian nieogrzewanych',
                'attr' => array(
                    'help' => 'Chodzi o te ściany twojego mieszkania, za którymi są nieogrzewane pomieszczenia (puste mieszkanie, klatka schodowa itp.)',
                )
            ))
            ->add('whats_over', 'choice', array(
                'choices' => array(
                    'heated_room' => 'Ogrzewany lokal',
                    'unheated_room' => 'Nieogrzewany lokal',
                    'outdoor' => 'Świat zewnętrzny',
                ),
                'label' => 'Co znajduje się nad twoim mieszkaniem?'
            ))
            ->add('whats_under', 'choice', array(
                'choices' => array(
                    'heated_room' => 'Ogrzewany lokal',
                    'unheated_room' => 'Nieogrzewany lokal lub piwnica',
                    'outdoor' => 'Świat zewnętrzny',
                    'ground' => 'Grunt',
                ),
                'label' => 'Co znajduje się pod twoim mieszkaniem?'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\Apartment',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'apartment';
    }
}
