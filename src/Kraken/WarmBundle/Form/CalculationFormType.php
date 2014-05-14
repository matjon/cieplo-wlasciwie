<?php

namespace Kraken\WarmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CalculationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('building_type', 'choice', array(
                'choices' => array(
                    'single_house' => 'Dom jednorodzinny wolnostojący',
                    'double_house' => 'Bliźniak',
                    'row_house' => 'Zabudowa szeregowa',
                    'apartment' => 'Mieszkanie',
                ),
                'label' => 'Rodzaj budynku',
                'attr' => array(
                    'class' => 'input-xlarge',
                ),
            ))
            ->add('construction_year', 'choice', array(
                'choices' => array_combine(range(date("Y"), 1900), range(date("Y"), 1900)),
                'required' => true,
                'label' => 'Rok budowy domu',
                'attr'  => array(
                    'help' => 'Dokładność +/- 10 lat nas zadowoli',
                    'class' => 'input-xlarge',
                ),
            ))
            ->add('fuel_type', 'choice', array(
                'choices' => array(
                    '' => 'Nie wiem/nie powiem',
                    'wood' => 'Drewno',
                    'gas_e' => 'Gaz ziemny typ E (GZ-50)',
                    'gas_ls' => 'Gaz ziemny typ Ls (GZ-35)',
                    'gas_lw' => 'Gaz ziemny typ Lw (GZ-41,5)',
                    'coke' => 'Koks',
                    'sand_coal' => 'Miał węglowy',
                    'pellet' => 'Pellet/brykiety',
                    'electricity' => 'Prąd elektryczny',
                    'brown_coal' => 'Węgiel brunatny',
                    'coal' => 'Węgiel kamienny',
                ),
                'required' => false,
                'label' => 'Czym ogrzewasz dom',
                'attr' => array(
                    'class' => 'input-xlarge',
                ),
            ))
            ->add('stove_type', 'choice', array(
                'choices' => array(
                    '' => 'Nie wiem/nie powiem',
                    'manual_upward' => 'Kocioł zasypowy górnego spalania',
                    'manual_downward' => 'Kocioł zasypowy dolnego spalania',
                    'automatic' => 'Kocioł podajnikowy',
                    'fireplace' => 'Kominek',
                    'kitchen' => 'Piec kuchenny',
                    'ceramic' => 'Piec kaflowy',
                    'goat' => 'Piec typu koza',
                ),
                'required' => false,
                'label' => 'Rodzaj pieca/kotła',
                'attr' => array(
                    'class' => 'input-xlarge',
                    'help' => 'Nie orientujesz się? Wybierz "górne spalanie".',
                ),
            ))
            ->add('stove_power', 'number', array(
                'label' => 'Moc kotła',
                'required' => false,
                'attr'  => array(
                    'append_input'  => 'kW'
                ),
            ))
            ->add('fuel_consumption', 'number', array(
                'label' => 'Zużycie opału ostatniej zimy',
                'required' => false,
                'attr'  => array(
                    'append_input'  => 't',
                    'help' => 'Jeśli grzejesz równocześnie ciepłą wodę, odejmij 10-20%',
                ),
            ))
            ->add('fuel_cost', 'number', array(
                'label' => 'Koszt zużytego opału',
                'required' => false,
                'attr'  => array(
                    'append_input'  => 'zł'
                ),
            ))
            ->add('email', null, array(
                'label' => 'Twój adres e-mail',
                'required' => false,
            ))
            ->add('indoor_temperature', 'number', array(
                'label' => 'Temperatura w mieszkaniu zimą',
                'attr'  => array(
                    'append_input'  => 'st.C',
                    'help' => 'Chodzi o średnią dobową. Jeśli np. w dzień zwykle jest 22st.C, a nocą 18st.C, to wpisz 20st.C',
                ),
            ))
            ->add('latitude', 'hidden')
            ->add('longitude', 'hidden')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\Calculation',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'calculation';
    }
}
