<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'First name',
                'attr' => [
                    'placeholder' => 'Please insert your first name here'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Last name',
                'attr' => [
                    'placeholder' => 'Please insert your last name here'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => [
                    'placeholder' => 'Please insert your e-mail address here'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password and confirmation do not match.',
                'label' => 'Password',
                'required' => true,
                'constraints' => new Length([
                    'min' => 6,
                    'max' => 55
                ]),
                'first_options' => ['label' => 'Password', 'attr' => [
                    'placeholder' => 'Enter a secure password'
                ]],
                'second_options' => ['label' => 'Confirm password', 'attr' => [
                    'placeholder' => 'Please re-enter your password'
                ]],
            ])
            // ->add('password_confirm', PasswordType::class, [
            //     'label' => 'Password confirmation',
            //     'mapped' => false,
            //     'attr' => [
            //         'placeholder' => 'Please re-enter your password'
            //     ]
            // ])
            ->add('submit', SubmitType::class, [
                'label' => 'SUBMIT'
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
