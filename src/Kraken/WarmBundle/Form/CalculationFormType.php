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
            ))
            ->add('construction_year', 'choice', array(
                'choices' => array_combine(range(date("Y"), 1900), range(date("Y"), 1900)),
                'required' => true,
                'label' => 'Rok budowy domu',
                'attr'  => array(
                    'help_text' => 'Dokładność +/- 10 lat nas zadowoli',
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
                    'help_text' => 'Nie orientujesz się? Wybierz "górne spalanie".',
                ),
            ))
            ->add('stove_power', 'number', array(
                'label' => 'Moc kotła',
                'required' => false,
                'attr'  => array(
                    'input_group' => array(
                        'append'  => 'kW'
                    )
                ),
            ))
            ->add('fuel_consumption', 'number', array(
                'label' => 'Zużycie opału ostatniej zimy',
                'required' => false,
                'attr'  => array(
                    'input_group' => array(
                        'append'  => 't',
                    ),
                    'help_text' => 'Jeśli grzejesz równocześnie ciepłą wodę, odejmij 10-20%',
                ),
            ))
            ->add('fuel_cost', 'number', array(
                'label' => 'Koszt zużytego opału',
                'required' => false,
                'attr'  => array(
                    'input_group' => array(
                        'append'  => 'zł'
                    )
                ),
            ))
            ->add('email', null, array(
                'label' => 'Twój adres e-mail',
                'required' => false,
            ))
            ->add('indoor_temperature', 'number', array(
                'label' => 'Temperatura w mieszkaniu zimą',
                'attr'  => array(
                    'input_group' => array(
                        'append'  => '&deg;C',
                    ),
                    'help_text' => 'Podaj średnią dobową temperaturę, jaką uznajesz za komfortową w domu zimą bez noszenia swetra i kaleson. Np. w dzień +22, w nocy +18 - wpisz 20 stopni',
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
