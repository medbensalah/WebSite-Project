<?php

namespace App\Form;

use App\Entity\User;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\List_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('firstName')
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('phone', NumberType::class)
            ->add('birth', BirthdayType::class)
            ->add('governorate',null, [
                'expanded' => false ,
                'multiple' => false,
                'mapped' =>false
            ])
            ->add('gender', ChoiceType::class, array(
                    'choices' => [
                        'male' => 'male',
                        'female' => 'female'
                    ],
                    'expanded' => true,
                    'multiple' => false)
            )
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
