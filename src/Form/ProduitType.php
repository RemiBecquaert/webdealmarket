<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

use App\Entity\EtatProduit;
use App\Entity\MarqueProduit;
use App\Entity\CategorieProduit;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'constraints' =>[
                    new Length([
                        'min' => 3, 
                        'max' => 255,
                        'minMessage' => 'Le titre de l\'article doit faire au minimum {{ limit }} caractères !',
                        'maxMessage' => 'Le titre de l\'article doit faire au maximum {{ limit }} caractères !'
                    ])
                ]
            ])
            ->add('idCategorie', EntityType::class, [
                'class' => CategorieProduit::class,
                'choice_label' => 'libelle'
            ])
            ->add('idMarque', EntityType::class, [
                'class' => MarqueProduit::class,
                'choice_label' => 'libelle'
            ])
            ->add('idEtat', EntityType::class, [
                'class' => EtatProduit::class,
                'choice_label' => 'libelle'
            ])
            ->add('subtitle', TextType::class, [
                'constraints' =>[
                    new Length([
                        'min' => 10, 
                        'max' => 255,
                        'minMessage' => 'Le sous-titre doit faire au minimum {{ limit }} caractères !',
                        'maxMessage' => 'Le sous-titre doit faire au maximum {{ limit }} caractères !'
                    ])
                ]
            ])
            ->add('description', TextareaType::class)
            ->add('illustration', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('quantite', IntegerType::class, [
                'constraints' =>[
                    new Type([
                        'type' => 'int',
                        'message' => 'Veuillez ajouter une quantité valide !'
                    ]),
                     new Length([
                        'min' => 1,
                        'minMessage' => 'Veuillez ajouter une quantité !',
                    ]),
                ]
            ])
            ->add('prix', IntegerType::class, [
                'constraints' =>[
                    new Type([
                        'type' => 'int',
                        'message' => 'Veuillez ajouter un prix valide !'
                    ]),
                     new Length([
                        'min' => 1,
                        'minMessage' => 'Veuillez ajouter un prix !',
                    ]),
                ]
            ])
            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
