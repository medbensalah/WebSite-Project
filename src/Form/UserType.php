<?php

namespace App\Form;

use App\Entity\User;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\List_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
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
            ->add('name', null, [
                'attr' => ['class' => 'name']
            ])
            ->add('firstName', null, array(
                'attr' => ['class' => 'firstName']
            ))
            ->add('gender', ChoiceType::class, array(
                'choices' => [
                    'male' => 'male',
                    'female' => 'female'
                ],
                'attr' => ['class' => 'gender']
            ))
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'email']
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'password']
            ])
            ->add('phone', NumberType::class, [
                'attr' => ['class' => 'phone']
            ])
            ->add('birth', BirthdayType::class, [
                'attr' => ['class' => 'birth']
            ])
            ->add('governorate', null, [
                'attr' => ['class' => 'governorate']
            ])
            ->add('photo', null, [
                'attr' => ['class' => 'photo']
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'submit']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
