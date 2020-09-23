<?php

namespace App\Form;

use App\Entity\Salle;
use App\Entity\TypeSalle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', NULL, ["required"=>true])
            ->add('nombrePlaces', NULL, ["required"=>false])
            ->add('type', EntityType::class, ["class"=>TypeSalle::class, "choice_label"=>"nom", "required"=>true])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Salle::class,
        ]);
    }
}
