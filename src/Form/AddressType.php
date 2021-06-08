<?php

namespace App\Form;

use App\Entity\Address;
use App\Form\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' =>'Nom de l\'adresse',
                'attr' =>[
                    'placeholder' => 'Maison',
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' =>'Prénom',
                'attr' =>[
                    'placeholder' => 'Jean',
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' =>'Nom',
                'attr' =>[
                    'placeholder' => 'Dupont',
                ]
            ])
            ->add('company', TextType::class, [
                'label' =>'Entreprise',
                'required' => false,
                'attr' =>[
                    'placeholder' => 'Optionnel',
                ]
            ])
            ->add('address', TextType::class, [
            'label' =>'Adresse',
                'attr' =>[
                    'placeholder' => '12 rue des oiseaux',
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' =>'Code postal',
                'attr' =>[
                    'placeholder' => '13001',
                ]
            ])
            ->add('city', TextType::class, [
                'label' =>'Ville',
                'attr' =>[
                    'placeholder' => 'Marseille',
                ]
            ])
            ->add('country', CountryType::class, [
                'preferred_choices' => ['FR'],
                'label' =>'Pays',
                'attr' =>[
                    'placeholder' => 'Pays',
                ]
            ])
            ->add('phone', TelType::class, [
                'label' =>'Téléphone',
                'attr' =>[
                    'placeholder' => '06 12 34 56 78',
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => "Valider l'adresse"
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
