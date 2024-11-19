<?php

namespace App\Form;

use App\Entity\Campaign;
use App\Entity\User;
use App\Entity\UserCampaign;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserCampaignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt', null, [
                'widget' => 'single_text',
                'label' => 'Date création'
            ])
            ->add('campaign', EntityType::class, [
                'class' => Campaign::class,
                'choice_label' => 'name',
                'label' => 'Campagne'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Utilisateur'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserCampaign::class,
        ]);
    }
}
