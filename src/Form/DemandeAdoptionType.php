<?php

namespace App\Form;

use App\Entity\DemandeAdoption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DemandeAdoptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('message', TextareaType::class, [
            'label' => 'Votre message à l’éleveur',
            'attr' => [
                'rows' => 6,
                'placeholder' => 'Expliquez pourquoi vous souhaitez adopter cet animal…',
            ],
            'constraints' => [
                new Assert\NotBlank(message: 'Le message est obligatoire.'),
                new Assert\Length(min: 10, minMessage: 'Merci d’écrire au moins {{ limit }} caractères.'),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeAdoption::class,
        ]);
    }
}
