<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Carrier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];


        $builder->add('address', EntityType::class, [
            'label' => 'Adresse de livraison',
            'class' => Address::class,
            'required' => true,
            'choices' => $user->getAddresses(),
            'multiple' => false,
            'expanded' => true
        ])
        ->add('carrier', EntityType::class, [
            'label' => 'Transporteur',
            'class' => Carrier::class,
            'required' => true,
            'multiple' => false,
            'expanded' => true
        ])
        ->add('submit', SubmitType::class, [
            'label' =>  'Payer',
            'attr' => [
                'class' => 'mt-2 btn-pay'
            ]
        ]
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'user' => [],
            // Configure your form options here
        ]);
    }
}
