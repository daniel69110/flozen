<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'attr' => [
                    'placeholder' => 'Entrez l\'email de l\'administrateur',
                    'class' => 'form-control',
                    'aria-label' => 'Adresse e-mail de l\'administrateur',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'email ne peut pas être vide',
                    ]),
                    new Assert\Email([
                        'message' => 'Veuillez entrer une adresse e-mail valide',
                    ]),
                ],
                'label' => 'Email',
                'required' => true,
            ])

            ->add('agreeTerms', HiddenType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle :',
                'choices' => [
                    'Administrateur' => "ROLE_ADMIN",
                ],
                'expanded' => true,
                'multiple' => true,
                'attr' => ['class' => 'mb-5'],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Entrez un mot de passe'
                    ],
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Le mot de passe ne peut pas être vide.',
                        ]),
                        new Assert\Length([
                            'min' => 8,
                            'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères.',
                        ]),
                        new Assert\Regex([
                            'pattern' => '/[A-Z]/',
                            'message' => 'Le mot de passe doit contenir au moins une majuscule.',
                        ]),
                        new Assert\Regex([
                            'pattern' => '/[a-z]/',
                            'message' => 'Le mot de passe doit contenir au moins une minuscule.',
                        ]),
                        new Assert\Regex([
                            'pattern' => '/\d/',
                            'message' => 'Le mot de passe doit contenir au moins un chiffre.',
                        ]),
                        new Assert\Regex([
                            'pattern' => '/[\W_]/',
                            'message' => 'Le mot de passe doit contenir au moins un caractère spécial.',
                        ]),
                    ]
                ],
                'second_options' => [
                    'label' => 'Répétez le mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmez votre mot de passe'
                    ]
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
