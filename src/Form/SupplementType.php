<?php

namespace App\Form;

use App\Entity\Supplement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupplementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('price', IntegerType::class, ['label' => 'Prix (CFA)', 'required' => false])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Par jour' => Supplement::PER_DAY,
                    'Par personne' => Supplement::PER_PERSON,
                    'Par reservation' => Supplement::PER_BOOKING,
                    'Par jour et par personne' => Supplement::PER_DAY_PERSON
                ],
                'label' => 'Par',
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-stone',
                ],
                'placeholder' => 'Par',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supplement::class,
        ]);
    }
}
