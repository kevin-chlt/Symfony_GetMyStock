<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class SubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label'=>'Nom d\'utilisateur',
                'attr'=>[
                    'class'=>'form-control mb-3'
                ],
                'label_attr'=>[
                    'class'=>'mb-3 fs-4 fst-italic'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label'=>'Mot de passe',
                'attr'=>[
                    'class'=>'form-control mb-3'
                ],
                'label_attr'=>[
                    'class'=>'mb-3 fs-4 fst-italic'
                ]
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Email',
                'attr'=>[
                    'class'=>'form-control mb-3'
                ],
                'label_attr'=>[
                    'class'=>'mb-3 fs-4 fst-italic'
                ],
                'invalid_message' => 'Une erreur à été détecté dans le mail, veuillez la corriger'
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
