<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $htmlAttr = [
            'minlength' => 8,
            'maxlength' => 4096,
        ];

        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'constraints' => [
                new NotBlank(['normalizer' => 'trim']),
                new Length([
                    'min' => 8,
                    'max' => 4096,
                ]),
            ],
            'first_options' => ['label' => 'Nouveau mot de passe', 'attr' => array_merge($htmlAttr)],
            'second_options' => ['label' => 'Confirmer le mot de passe', 'attr' => array_merge($htmlAttr)],
        ]);
    }
}
