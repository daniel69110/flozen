<?php

namespace App\Form;

use App\DTO\ContactDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => '',
                'attr' => ['placeholder' => 'Entrez le titre de votre message'], // Placeholder ajouté
            ])
            ->add('email', EmailType::class, [
                'empty_data' => '',
                'attr' => ['placeholder' => 'Entrez votre email'], // Placeholder ajouté
            ])
            ->add('message', TextareaType::class, [
                'empty_data' => '',
                'attr' => ['placeholder' => 'Rédigez votre message ici'], // Placeholder ajouté
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDTO::class,
        ]);
    }
}
