<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'John',
                ],
                'required' => true,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Doe',
                ],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'john@example.com',
                ],
                'required' => true,
            ])

            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'S3CR3T',
                    ],
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'max' => 4096,
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
                            'message' => 'Le mot de passe doit contenir au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial, et doit être d’au moins 8 caractères de long.',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation',
                    'attr' => [
                        'placeholder' => 'S3CR3T',
                    ],
                ],
                'mapped' => false,
                'required' => true,
            ]);

        if ($options['isAdmin']) {
            $builder
            ->remove('password')
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'isAdmin' => false,
            'sanitize_html' => true,
        ]);
    }
}
