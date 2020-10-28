<?php

namespace App\Form\moderator;

use App\Entity\Conge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CongeModeratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('date_debut')*/
            /*->add('date_fin', null, [
                'label' => 'Date fin'
            ])*/
            /*->add('motif')*/
            ->add('vue', null, [
                'label' => 'Viser la demande ?'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Conge::class,
        ]);
    }
}
