<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\Supplement;
use App\Entity\Room;
use App\Entity\RoomEquipment;
use App\Entity\Taxe;
use App\Repository\OptionRepository;
use App\Repository\SupplementRepository;
use App\Repository\RoomEquipmentRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de l\'hébergement'])
            ->add('smoker', ChoiceType::class, [
                'choices' => [
                    'Non-fumeurs' => 'Non-fumeurs',
                    'Fumeurs' => 'Fumeurs',
                    'Cet hébergement est fumeurs et non-fumeurs' => 'Cet hébergement est fumeurs et non-fumeurs',
                ],
                'label' => 'Fumeurs ou non-fumeurs',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-stone'],
                'placeholder' => 'Fumeurs ou non-fumeurs',
                'required' => false,
            ])
            ->add('description', CKEditorType::class, [
                'label' => false,
                'config' => ['height' => '150', 'uiColor' => '#ffffff', 'toolbar' => 'basic']
            ])
            ->add('roomNumber', IntegerType::class, ['label' => 'Nombre d\'hébergements (de ce type)'])
            ->add('price', IntegerType::class, ['label' => 'Tarif (en CFA)'])
            ->add('maximumAdults', IntegerType::class, ['label' => 'Nombre maximum d\'adultes'])
            ->add('maximumOfChildren', IntegerType::class, ['label' => 'Nombre maximum d\'enfants'])
            ->add('area', IntegerType::class, ['label' => 'Superficie de l\'hébergement (m²)', 'required' => false,])
            ->add('enabled', CheckboxType::class, ['label' => 'Activé', 'required' => false])
            ->add('taxeStatus', CheckboxType::class, ['label' => 'La taxe est-il dans le prix de l\'hébergement ?', 'required' => false])
            ->add('equipments', EntityType::class, [
                'class' => RoomEquipment::class,
                'choice_label' => 'name',
                'query_builder' => function (RoomEquipmentRepository $er) {
                    return $er->getData();
                },
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Équipements de chambre',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
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
            ->add('options', EntityType::class, [
                'class' => Option::class,
                'choice_label' => 'name',
                'query_builder' => function (OptionRepository $er) {
                    return $er->getData();
                },
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Options',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('taxe',  EntityType::class, [
                'label' => 'Taxe',
                'class' => Taxe::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-stone mb-0',
                ],
                'placeholder' => 'Taxe',
            ])
            ->add('couchage', TextType::class, [
                'label' => 'Couchage (Lit de la chambre)',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
