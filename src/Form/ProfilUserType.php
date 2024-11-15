<?php

namespace App\Form;

use App\Entity\ProfilUser;
use App\Entity\User;
use App\Enum\SexeEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Entrez votre adresse',
                ],
                'constraints' => [
                    new Assert\Length(['max' => 255]),
                    new Assert\NotBlank(['message' => 'L\'adresse ne peut pas être vide.']),
                ],
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez votre ville',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La ville est obligatoire.']),
                    new Assert\Length(['max' => 100]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez votre nom de famille',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom de famille est obligatoire.']),
                    new Assert\Length(['max' => 50]),
                ],
            ])
            ->add('firstName', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez votre prénom',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Assert\Length(['max' => 50]),
                ],
            ])
            ->add('postalCode', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez votre code postal',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le code postal est obligatoire.']),
                    new Assert\Length(['max' => 10]),
                    new Assert\Regex([
                        'pattern' => '/^\d{5}$/',
                        'message' => 'Le code postal doit être composé de 5 chiffres.',
                    ]),
                ],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Entrez votre numéro de téléphone',
                    'maxlength' => 20
                ],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\+?[0-9]{10,15}$/',
                        'message' => 'Le numéro de téléphone doit être valide.',
                    ]),
                ],
            ])
            ->add('sexe', EnumType::class, [
                'class' => SexeEnum::class,
                'placeholder' => 'Sélectionner le genre',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilUser::class,
            'csrf_protection' => true,
        ]);
    }
}
