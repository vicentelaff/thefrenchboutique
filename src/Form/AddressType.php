<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'What is the name of this address ?',
                'attr' => [
                    'placeholder' => 'Address name'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'First name',
                'attr' => [
                    'placeholder' => 'Enter your first name...'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Last name',
                'attr' => [
                    'placeholder' => 'Enter your last name...'
                ]
            ])
            ->add('company', TextType::class, [
                'label' => 'Company',
                'required' => 'false',
                'attr' => [
                    'placeholder' => '(Optional) Enter the name of your company...'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Address',
                'attr' => [
                    'placeholder' => '3500 Deer Creek Road Palo Alto, CA 94304...'
                ]
            ])
            ->add('postal', TextType::class, [
                'label' => 'Postal code',
                'attr' => [
                    'placeholder' => 'Enter your postal code...'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'attr' => [
                    'placeholder' => 'Enter the name of your city...'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Country',
                'attr' => [
                    'placeholder' => 'Enter the name of your country...'
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone number',
                'attr' => [
                    'placeholder' => 'Enter your phone number...'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Confirm',
                'attr' => [
                    'class' => 'btn-block btn-outline-info text-uppercase mt-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
