<?php

namespace App\Form\admin;

use App\Entity\Conge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CongeAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('date_debut')*/
            /*->add('date_fin', null, [
                'label' => 'Date fin'
            ])*/
            /*->add('motif')*/
            ->add('etat', null, [
                'label' => 'Valider la demande.'
            ])
            /*->add('created_at')*/
            /*->add('utilisateur')*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Conge::class,
        ]);
    }
}
