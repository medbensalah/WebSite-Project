<?php

namespace App\Form;

use App\Entity\Gouvernorat;
use App\Entity\User;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\List_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email', EmailType::class)
            ->add('motDePasse', PasswordType::class)
            ->add('telephone', NumberType::class)
            ->add('dateDeNaissance', BirthdayType::class)

            ->add('gouvernorat',EntityType::class,[
                'class' => Gouvernorat::class
            ])
            ->add('sexe', ChoiceType::class, array(
                    'choices' => [
                        'male' => 'male',
                        'femelle' => 'femelle',
                        'entreprise' => 'entreprise'
                    ],
                    'expanded' => true,
                    'multiple' => false
                )
            )
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '50', 'rows' => '6'),
                'required' => false
            ])
            ->add('confirmer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
