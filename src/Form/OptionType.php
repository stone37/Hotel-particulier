<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\Supplement;
use App\Repository\SupplementRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('supplements', EntityType::class, [
                'class' => Supplement::class,
                'choice_label' => 'name',
                'query_builder' => function (SupplementRepository $er) {
                    return $er->getData();
                },
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Suppléments',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('description', CKEditorType::class, [
                'label' => false,
                'config' => ['height' => '150', 'uiColor' => '#ffffff', 'toolbar' => 'basic']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Option::class,
        ]);
    }
}
