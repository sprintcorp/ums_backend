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

class UpdateAccountFormType extends AbstractType
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
                    ])
                ]
            ])
            ->add('email', EmailType::class)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('othernames', TextType::class)
            ->add('roles',TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            //'allow_extra_fields' => true,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
    }
}
