<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('firstname', TextType::class,[
                'attr' => ['onkeyup' => 'getFirstName()'],
            ])
            ->add('lastname', TextType::class,[
                'attr' => ['onkeyup' => 'getLastName()'],
            ])
            ->add('company', TextType::class,[
                'attr' => ['onkeyup' => 'getCompany()'],
                'required' => false
            ])
            ->add('address', TextType::class,[
                'attr' => ['onkeyup' => 'getAddress()'],
            ])
            ->add('postal', TextType::class,[
                'attr' => ['onkeyup' => 'getPostal()'],
            ])
            ->add('city', TextType::class,[
                'attr' => ['onkeyup' => 'getCity()'],
            ])
            ->add('country', CountryType::class,[
                'attr' => ['onkeyup' => 'getCountry()'],
            ])
            ->add('phone', TelType::class,[
                'attr' => ['onkeyup' => 'getPhone()'],
            ])
            ->add('submit', SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
