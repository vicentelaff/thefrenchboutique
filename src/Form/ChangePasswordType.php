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

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true,
                'label' => 'My email'
            ])
            ->add('firstname', TextType::class, [
                'disabled' => true,
                'label' => 'My first name'
            ])
            ->add('lastname', TextType::class, [
                'disabled' => true,
                'label' => 'My last name'
            ])
            ->add('old_password', PasswordType::class, [
                'label' => 'My password',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Please enter your current password'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'The password and confirmation do not match.',
                'label' => 'New password',
                'required' => true,
                'constraints' => new Length([
                    'min' => 6,
                    'max' => 30
                ]),
                'first_options' => ['label' => 'New password', 'attr' => [
                    'placeholder' => 'Enter a new secure password'
                ]],
                'second_options' => ['label' => 'Confirm new password', 'attr' => [
                    'placeholder' => 'Please re-enter your new password'
                ]],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'SUBMIT'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
