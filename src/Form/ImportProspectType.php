<?php

namespace App\Form;

use App\Entity\Campaign;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportProspectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campaign', EntityType::class, [
                'class' => Campaign::class,
                'choice_label' => 'name',
                'label' => 'Campagne',
            ])
            ->add('file', FileType::class, [
                'label' => 'Fichier XLS',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Importer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_token' => false
        ]);    }
}
