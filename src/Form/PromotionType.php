<?php

namespace App\Form;

use App\Entity\Promotion;
use App\Entity\Room;
use App\Repository\RoomRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('description', CKEditorType::class, [
                'label' => false,
                'config' => ['height' => '150', 'uiColor' => '#ffffff', 'toolbar' => 'basic']
            ])
            ->add('start', DateType::class, [
                'label' => 'Date de Debut',
                'widget' => 'single_text',
            ])
            ->add('end', DateType::class, [
                'label' => 'Date de Fin',
                'widget' => 'single_text',
            ])
            ->add('discount', IntegerType::class, ['label' => 'Reduction (En %)'])
            ->add('enabled', CheckboxType::class, ['label' => 'Activé', 'required' => false])
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'name',
                'query_builder' => function (RoomRepository $er) {
                    return $er->roomListeQueryBuilder();
                },
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-stone'],
                'label' => 'Hébergement',
                'placeholder' => 'Hébergement'
            ])
            ->add('file', VichFileType::class, ['label' => 'Logo du site', 'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
