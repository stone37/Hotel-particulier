<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, ['label' => 'Nom de l\'hotel'])
            ->add('email',TextType::class, ['label' => 'E-mail'])
            ->add('phone',TextType::class, ['label' => 'Telephone'])
            ->add('fax',TextType::class, ['label' => 'Fax', 'required' => false])
            ->add('address',TextType::class, ['label' => 'Adresse', 'required' => false])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr'  => ['class' => 'form-control md-textarea', 'rows'  => 5],
                'required' => false
            ])
            ->add('country', TextType::class, ['label' => 'Pays', 'required' => false])
            ->add('city', TextType::class, ['label' => 'Ville', 'required' => false])
            ->add('facebookAddress', TextType::class, ['label' => 'Adresse Facebook', 'required' => false])
            ->add('twitterAddress', TextType::class, ['label' => 'Adresse Twitter', 'required' => false])
            ->add('linkedinAddress', TextType::class, ['label' => 'Adresse Linkedin', 'required' => false])
            ->add('instagramAddress', TextType::class, ['label' => 'Adresse Instagram', 'required' => false])
            ->add('youtubeAddress', TextType::class, ['label' => 'Adresse Youtube', 'required' => false])
            ->add('checkinTime', TimeType::class, ['label' => 'Heure d\'arrivée', 'widget' => 'single_text',])
            ->add('checkoutTime', TimeType::class, ['label' => 'Heure de Départ', 'widget' => 'single_text'])
            ->add('file', VichFileType::class, ['label' => 'Logo du site', 'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
