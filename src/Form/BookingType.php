<?php

namespace App\Form;

use App\Entity\Availability;
use App\Entity\Booking;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDateTime', null, [
                'widget' => 'single_text',
            ])
            ->add('endDateTime', null, [
                'widget' => 'single_text',
            ])
            ->add('name', ChoiceType::class, [
                'choices' => [
                    'Massage Californien' => 'californien',
                    'Massage Suédois' => 'suédois',
                    'Madérothérapie' => 'madérothérapie',
                ],
                'label' => 'Type de Massage',
            ])
            
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
