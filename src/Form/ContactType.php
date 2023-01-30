<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\SujetContact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => ([
                    'min' => 2,
                    'max' => 255,
                    'minMessage' => 'Le nom doit comporter au minimum {{ limit }} caractères',
                    'maxMessage' => 'Le nom doit comporter au maximum {{ limit }} caractères'
                ])
            ])
            ->add('prenom', TextType::class, [
                'constraints' => ([
                    'min' => 2,
                    'max' => 255,
                    'minMessage' => 'Le prénom doit comporter au minimum {{ limit }} caractères',
                    'maxMessage' => 'Le prénom doit comporter au maximum {{ limit }} caractères'
                ])
            ])
            ->add('email', EmailType::class, [
                'constraints' => ([
                    'min' => 6,
                    'max' => 255,
                    'minMessage' => 'L\'email doit comporter au minimum {{ limit }} caractères',
                    'maxMessage' => 'L\'email doit comporter au maximum {{ limit }} caractères'
                ])
            ])
            ->add('idSujet', EntityType::class, [
                'class' => SujetContact::class,
                'choice_label' => 'libelle'
            ])
            ->add('texte', TextareaType::class) 
            ->add('envoyer', SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
