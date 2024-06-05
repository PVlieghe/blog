<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('username', TextType::class, [
            'label' => '<i class="bi bi-person"></i>Nom d\'utilisateur',
            'label_html' => true,
            'attr' => ['class'=>'form-control custom-form-control p-2 m-2']
        ])
        ->add('email', EmailType::class,[
            'label' => '<i class= "bi bi-envelope"></i> Email',
            'label_html' => true,
            'attr' => ['class'=>'form-control custom-form-control p-2 m-2'],
            'constraints' => [
                new NotBlank([
                    'message' => "L'email ne peut pas être vide.",
                ]),
                new Email([
                    'message' => "L'email '{{ value }}' n'est pas un email valide.",
                ]),
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
