<?php

namespace App\Form;

use App\Entity\Salle;
use App\Repository\SalleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FindPresenceType extends AbstractType
{
    private $salleRepository;

    private $date;

    public function __construct(SalleRepository $salleRepository)
    {
        $this->salleRepository = $salleRepository;
        $this->date = new \DateTime();
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', DateTimeType::class, ["required"=>false, 'data'=>$this->date])
            ->add('dateFin', DateTimeType::class, ["required"=>false, 'data'=>$this->date])
            ->add('username', TextType::class, ["required"=>false])
            ->add('salle', EntityType::class, ["required"=>false, "class"=>Salle::class, "choice_label"=>"nom"])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
