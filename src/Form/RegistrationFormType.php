<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class'=>'form-control custom-form-control p-2 m-2'
                    ],
                'label_html' => true,
                'label' => "Mot de passe",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer une mot de passe.',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Votre mot de passe doit au moins contenir {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
