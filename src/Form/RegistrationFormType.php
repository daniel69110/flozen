<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Entrez votre adresse email'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un email.']),
                    new Email(['message' => 'Veuillez entrer un email valide.']),
                ],
            ])
        
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])

            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Mots de passe incorrect.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Password',
                    'attr' => ['placeholder' => 'Entrez votre mot de passe'],
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr' => ['placeholder' => 'Répétez votre mot de passe'],
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'Votre mot de passe doit contenir au moins une lettre majuscule.',
                    ]),
                    new Regex([
                        'pattern' => '/[a-z]/',
                        'message' => 'Votre mot de passe doit contenir au moins une lettre minuscule.',
                    ]),
                    new Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'Votre mot de passe doit contenir au moins un chiffre.',
                    ]),
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
