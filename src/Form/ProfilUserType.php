<?php

namespace App\Form;

use App\Entity\ProfilUser;
use App\Entity\User;
use App\Enum\SexeEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('address')
            ->add('city')
            ->add('lastName')
            ->add('firstName')
            ->add('postalCode')
            ->add('phone', TextType::class, [
                'required' => false,
                'attr' => [
                    'maxlength' => 20,
                ],
            ])
            ->add('sexe', EnumType::class, [
                'class' => SexeEnum::class,
                'placeholder' => 'Genre',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilUser::class,
        ]);
    }
}
