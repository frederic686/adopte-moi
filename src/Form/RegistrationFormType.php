<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'Email requis.'),
                    new Assert\Email(message: 'Email invalide.'),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(message: 'Votre nom est requis.'),
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'constraints' => [ new Assert\Length(max: 255) ],
            ])

            // ✅ Input de fichier (non mappé)
            ->add('photoFile', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*'], // ouvre la galerie / filtre images
                'constraints' => [
                    new Image(
                        maxSize: '2M',
                        maxSizeMessage: 'Image trop lourde (max 2 Mo).',
                        detectCorrupted: true
                    ),
                ],
            ])

            // Mot de passe + confirmation
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'first_options'  => ['label' => 'Mot de passe', 'attr' => ['autocomplete' => 'new-password']],
                'second_options' => ['label' => 'Confirmez le mot de passe', 'attr' => ['autocomplete' => 'new-password']],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Mot de passe requis.'),
                    new Assert\Length(min: 6, minMessage: 'Au moins {{ limit }} caractères.'),
                ],
            ])

            ->add('isEleveur', CheckboxType::class, [
                'label' => 'Je suis éleveur',
                'mapped' => false,
                'required' => false,
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'label' => "J'accepte les CGU",
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez accepter les CGU pour continuer.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
