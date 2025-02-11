<?php

namespace App\Form;

use App\Entity\ProfilUser;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('oldPassword', PasswordType::class, [
            'mapped' => false,
            'attr' => ['placeholder' => 'Entrez votre ancien mot de passe'],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le mot de passe actuel est requis.',
                ]),
            ],
        ])
        ->add('newPassword', RepeatedType::class, [
            'mapped' => false,
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe ne correspondent pas.',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => [
                'label' => 'Nouveau mot de passe',
                'attr' => ['placeholder' => 'Entrez votre nouveau mot de passe'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nouveau mot de passe est requis.',
                    ]),
                    new Assert\Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[A-Za-z]/',
                        'message' => 'Le mot de passe doit contenir au moins une lettre.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'Le mot de passe doit contenir au moins un chiffre.',
                    ]),
                ],
            ],
            'second_options' => [
                'label' => 'Répétez le mot de passe',
                'attr' => ['placeholder' => 'Répétez votre nouveau mot de passe'],
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
