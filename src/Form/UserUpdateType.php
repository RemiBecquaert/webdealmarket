<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class UserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'empty_data' => User::class,
                'constraints' => [
                    new Length([
                    'min' => 2,
                    'max' => 255,
                    'minMessage' => 'Le nom doit contenir au minimum {{ limit }} caractères',
                    'maxMessage' => 'Le nom doit contenir au maximum {{ limit }} caractères'
                ])]
            ])
            ->add('prenom', TextType::class, [
                'empty_data' => User::class,
                'constraints' => [
                    new Length([
                    'min' => 2,
                    'max' => 255,
                    'minMessage' => 'Le prénom doit contenir au minimum {{ limit }} caractères',
                    'maxMessage' => 'Le prénom doit contenir au maximum {{ limit }} caractères'
                ])]
            ])
            ->add('envoyer', SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
