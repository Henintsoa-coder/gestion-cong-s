<?php

namespace App\Form\admin;

use App\Entity\Permission;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminPermissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_debut')
            ->add('date_fin')
            ->add('motif')
            ->add('etat')
            /*->add('created_at')
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'required' => false,
                'choice_label' => 'id'
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Permission::class,
        ]);
    }
}
