<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Veuillez renseigner le nom de votre adresse',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom de votre adresse est trop court !',
                        'max' => 255,
                        'maxMessage' => 'Le nom de votre adresse est trop long !'
                    ])
                ]
            ])
            ->add('firstname', TextType::class,[
                'attr' => ['onkeyup' => 'getFirstName()'],
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Veuillez renseigner un nom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom renseigné est trop court !',
                        'max' => 255,
                        'maxMessage' => 'Le nom renseigné est trop long !'
                    ])
                ]
            ])
            ->add('lastname', TextType::class,[
                'attr' => ['onkeyup' => 'getLastName()'],
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Veuillez renseigner un prénom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le prénom renseigné est trop court !',
                        'max' => 255,
                        'maxMessage' => 'Le prénom renseigné est trop long !'
                    ])
                ]
            ])
            ->add('company', TextType::class,[
                'attr' => ['onkeyup' => 'getCompany()'],
                'required' => false,
                'constraints' =>[
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'L\'entreprise renseignée est trop longue'
                    ])
                ]
            ])
            ->add('address', TextType::class,[
                'attr' => ['onkeyup' => 'getAddress()'],
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Veuillez indiquer une adresse',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'L\'adresse renseignée est trop courte !',
                        'max' => 255,
                        'maxMessage' => 'L\'adresse renseignée est trop longue !'
                    ])
                ]
            ])
            ->add('postal', IntegerType::class,[
                'attr' => ['onkeyup' => 'getPostal()'],
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Veuillez indiquer votre code postal',
                    ]),
                    new Type([
                        'type'=> 'integer',
                        'message' => 'La valeur du code postal doit être une valeur numérique'
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Le code postal renseigné est trop court !',
                        'max' => 6,
                        'maxMessage' => 'Le code postal renseigné est trop long !'
                    ])
                ]
            ])
            ->add('city', TextType::class,[
                'attr' => ['onkeyup' => 'getCity()'],
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Veuillez indiquer la ville de résidence',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom de ville renseigné est trop court !',
                        'max' => 255,
                        'maxMessage' => 'Le nom de ville renseigné est trop long !'
                    ])
                ]
            ])
            ->add('country', CountryType::class,[
                'attr' => ['onkeyup' => 'getCountry()'],
            ])
            ->add('phone', IntegerType::class,[
                'constraints' =>[
                    new Type([
                        'type' => 'int',
                        'message' => 'La valeur du téléphone doit être une valeur numérique'
                    ]),
                     new Length([
                        'min' => 6,
                        'minMessage' => 'Le numéro de téléphone renseigné est trop court !',
                        'max' => 11,
                        'maxMessage' => 'Le numéro de téléphone renseigné est trop long !'
                    ]),
                ],
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
