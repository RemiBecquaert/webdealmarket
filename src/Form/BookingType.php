<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\BookingSujet;
use App\Entity\User;
use App\Entity\MarqueProduit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class)
            ->add('beginAt', DateTimeType::class, [
                'widget' => 'single_text'
                ])
            ->add('sujetId', EntityType::class, [
                'class' => BookingSujet::class,
                'choice_label' => 'libelle'
            ])
            ->add('marque', EntityType::class, [
                'class' => MarqueProduit::class,
                'choice_label' => 'libelle',
                'empty_data' => 'AUTRE'
            ])
            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
