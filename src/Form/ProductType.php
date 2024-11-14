<?php

namespace App\Form;


use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Validator\Constraints as Assert;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', VichFileType::class, [
                'label' => false,
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M', 
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'], 
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF)', 
                    ]),
                ],
            ])
            ->add('name')
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                    'rows' => 5, // Définit la hauteur du textarea
                    'placeholder' => 'Écrivez la description ici...',
                ],
            ])
            ->add('price')


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
