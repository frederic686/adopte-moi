<?php

namespace App\Form;

use App\Entity\Animal;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('race')
           ->add('sexe', ChoiceType::class, [
                'choices'  => [
                    'Mâle' => 'male',
                    'Femelle' => 'femelle',
                ],
                'placeholder' => 'Choisir le sexe',
                'expanded' => false,   // false = <select>, true = radio boutons
                'multiple' => false,   // on choisit une seule valeur
            ])

            ->add('age')

            // ✅ Upload obligatoire PNG/JPEG
            ->add('photoFile', FileType::class, [
                'label' => 'Photo de l’animal',
                'mapped' => false,
                'required' => true,
                'attr' => ['accept' => 'image/png, image/jpeg'],
                'constraints' => [
                    new NotBlank(['message' => 'Une photo est obligatoire']),
                    new Image([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Image trop lourde (max 5 Mo)',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Seules les images JPEG ou PNG sont autorisées.',
                    ]),
                ],
            ])


            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'categorie',
                'placeholder' => 'Choisir un type',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Animal::class]);
    }
}
