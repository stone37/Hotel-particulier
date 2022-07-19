<?php

namespace App\Form;

use App\Data\BookingData;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class BookingType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('occupants', CollectionType::class, [
                'entry_type' => RoomUserType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Veuillez écrire vos demandes (facultatif)',
                'attr'  => ['class' => 'form-control md-textarea', 'rows'  => 4],
                'required' => false
            ]);

        /** @var ?User $user */
        $user = $this->security->getUser();

        if (!$user || empty($user->getFirstName())) {
            $builder->add('firstname', TextType::class, ['label' => 'Prénom']);
        }

        if (!$user || empty($user->getLastName())) {
            $builder->add('lastname', TextType::class, ['label' => 'Nom']);
        }

        if (!$user || empty($user->getEmail())) {
            $builder->add('email', EmailType::class, ['label' => 'Adresse e-mail']);
        }

        if (!$user || empty($user->getPhone())) {
            $builder->add('phone', TextType::class, ['label' => 'Numéro de téléphone']);
        }

        if (!$user || empty($user->getCountry())) {
            $builder->add('country', CountryType::class, [
                'label' => 'Pays (facultatif)',
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-stone',
                ],
                'placeholder' => 'Pays',
                'required' => false,
            ]);
        }

        if (!$user || empty($user->getCity())) {
            $builder->add('city', TextType::class, ['label' => 'Ville (facultatif)', 'required' => false]);
        }



        /*->add('firstname', TextType::class, ['label' => 'Prénom'])
        ->add('lastname', TextType::class, ['label' => 'Nom'])
        ->add('email', EmailType::class, ['label' => 'Adresse e-mail'])
        ->add('phone', TextType::class, ['label' => 'Numéro de téléphone'])
        ->add('country', CountryType::class, [
            'label' => 'Pays (facultatif)',
            'attr' => [
                'class' => 'mdb-select md-outline md-form dropdown-stone',
            ],
            'placeholder' => 'Pays',
            'required' => false,
        ])
        ->add('city', TextType::class, ['label' => 'Ville (facultatif)', 'required' => false]);*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookingData::class,
            'validation_groups' => ['booking']
        ]);
    }
}
