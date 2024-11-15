<?php

namespace App\Form;

use App\Entity\Campaign;
use App\Entity\Prospect;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProspectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campaign', EntityType::class, [
                'class' => Campaign::class,
                'choice_label' => 'name',
                'label' => 'Campagne'
            ])
            ->add('company', TextType::class, [
                'label' => 'Société'
            ])
            ->add('activity', TextType::class, [
                'label' => 'Activité'
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Codee Postal'
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville'
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone'
            ])
            ->add('mobile', TextType::class, [
                'label' => 'Mobile'
            ])
            ->add('email')
            ->add('commentary', TextType::class, [
                'label' => 'Commentaire'
            ])
            ->add('rappel', null, [
                'widget' => 'single_text',
                'label' => 'Rappel'
            ])
            ->add('rendezvous', null, [
                'widget' => 'single_text',
                'label' => 'Rendez-vous'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prospect::class,
        ]);
    }
}
