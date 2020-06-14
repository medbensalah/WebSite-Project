<?php

namespace App\Form;

use App\Entity\Gouvernorat;
use App\Entity\Job;
use Proxies\__CG__\App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class,[
                'class' => Categories::class
            ])
            ->add('image', FileType::class, array(
                'mapped'=> false,
                'required' => false,
                'constraints'=> array(
                    new Image()
                )
            ))
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '50', 'rows' => '6')
            ])
            ->add('confirmer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
