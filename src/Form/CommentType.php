<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Content', TextType::class, [
                'label' => '<i class="bi bi-text-center"></i>Votre commentaire',
                'label_html' => true,
                'attr' => ['class'=>'form-control custom-form-control p-2 m-2'],
                'constraints' => [
                    new NotBlank([
                        'message' => "Votre commentaire ne peut pas être vide",
                    ]),
                ]
            ])
            ->add('rate', IntegerType::class, [
                'label' => '<i class="bi bi-text-123"></i>Votre note /5',
                'label_html' => true,
                'attr' => ['class'=>'form-control custom-form-control p-2 m-2'],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez ajouter une note sur 5.",
                    ]),
                    new Range([
                        'min' => 0,
                        'max' => 5,
                        'notInRangeMessage' => 'La note doit être comprise entre {{ min }} et {{ max }}.'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Commenter', // Texte du bouton
                'attr' => [
                    'class' => 'btn btn-primary', // Classe CSS du bouton
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
