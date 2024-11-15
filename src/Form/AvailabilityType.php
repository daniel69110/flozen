<?php

namespace App\Form;

use App\Entity\Availability;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvailabilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('startDateTime', DateTimeType::class, [
            'widget' => 'single_text',
            'html5' => true,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'La date de début ne peut pas être vide.',
                ]),
            ],
            'attr' => [
                'placeholder' => 'Entrez la date et l\'heure de début',
            ],
        ])
        ->add('endDateTime', DateTimeType::class, [
            'widget' => 'single_text',
            'html5' => true,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'La date de fin ne peut pas être vide.',
                ]),
                new Assert\Callback(function ($value, $context) {
                    $startDateTime = $context->getRoot()->get('startDateTime')->getData();
    
                    if ($startDateTime instanceof \DateTimeInterface && $value instanceof \DateTimeInterface) {
                        if ($startDateTime >= $value) {
                            $context->buildViolation('La date de fin doit être postérieure à la date de début.')
                                ->addViolation();
                        }
                    }
                }),
            ],
            'attr' => [
                'placeholder' => 'Entrez la date et l\'heure de fin',
            ],
        ]);
            // ->add('isAvailable', CheckboxType::class, [
            //     'label' => 'Disponible',
                
            // ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Availability::class,
        ]);
    }
}
