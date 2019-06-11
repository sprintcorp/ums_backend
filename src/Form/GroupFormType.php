<?php

namespace App\Form;

use App\Entity\UserGroup;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Form\AbstractType;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\Length;


class GroupFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$nameRegex = new Regex([
            'value' => '/^[a-zA-Z0-9_\-]$/',
            'message' => 'Invalid name.'
        ]);*/

        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 25,
                        'minMessage' => 'Your name should be at least {{ limit }} characters',
                        'maxMessage' => 'Your name should not be longer than {{ limit }} characters',
                    ])
                ]
            ])

            ->add('roles',TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserGroup::class,
            //'allow_extra_fields' => true,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
    }
}
