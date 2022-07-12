<?php

namespace App\Form;

use App\Data\BookingData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('checkin', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'checkin-datepicker d-none'],
                'html5' => false
            ])
            ->add('checkout', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'checkout-datepicker d-none'],
                'html5' => false,
            ])
            ->add('adult', IntegerType::class, ['attr' => ['class' => 'booking_data_adult d-none']])
            ->add('children', IntegerType::class, ['attr' => ['class' => 'booking_data_children d-none']])
            ->add('roomNumber', IntegerType::class, ['attr' => ['class' => 'booking_data_room d-none']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookingData::class,
        ]);
    }
}


