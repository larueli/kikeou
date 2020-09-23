<?php

namespace App\Form;

use App\Entity\Presence;
use App\Entity\Salle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numeroPlace', NULL, ["required"=>true])
            ->add('dateDebut', DateTimeType::class, ["required"=>true])
            ->add('dateFin', DateTimeType::class,["required"=>true])
            ->add('salle', EntityType::class, ["class"=>Salle::class, "choice_label"=>"nom", "required"=>true])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Presence::class,
        ]);
    }
}
