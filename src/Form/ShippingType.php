<?php

namespace App\Form;

use App\Entity\Shipping;
use App\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ShippingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('address')
            ->add('additional_address')
            ->add('city')
            ->add('postcode')
            ->add('phone')
            // ->add('created_at')
            ->add('country', EntityType::class,
            [
                'placeholder' => 'Select a country',
                'class' => Country::class,
                'choice_label' => 'name',
                'multiple' => false,
                'required' => false,
            ])
            // ->add('client')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shipping::class,
        ]);
    }
}
