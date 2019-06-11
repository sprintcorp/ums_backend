<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$nameRegex = new Regex([
            'value' => '/^[a-zA-Z0-9_\-]$/',
            'message' => 'Invalid name.'
        ]);*/

        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 25,
                        'minMessage' => 'Your username should be at least {{ limit }} characters',
                        'maxMessage' => 'Your username should not be longer than {{ limit }} characters',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a username',
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email address',
                    ]),
                ]
            ])
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('othernames', TextType::class)
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'max' => 100,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'maxMessage' => 'Your password should not be longer than {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                    ]),
                ],
            ])
            ->add('roles',TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_extra_fields' => true,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
    }
}
