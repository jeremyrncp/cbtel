<?php

namespace App\Form;

use App\Entity\Campaign;
use App\Entity\Prospect;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ])
            ->add('company')
            ->add('activity')
            ->add('address')
            ->add('postalCode')
            ->add('city')
            ->add('phone')
            ->add('mobile')
            ->add('email')
            ->add('createdat', null, [
                'widget' => 'single_text',
            ])
            ->add('commentary')
            ->add('rappel', null, [
                'widget' => 'single_text',
            ])
            ->add('rendezvous', null, [
                'widget' => 'single_text',
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
