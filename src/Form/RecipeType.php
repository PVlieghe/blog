<?php

namespace App\Form;

use App\Entity\Step;
use App\Entity\User;
use App\Entity\Recipe;
use App\Form\StepType;
use App\Form\IngredientType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('steps', CollectionType::class, [
            'entry_type' => StepType::class, // Type de champ pour chaque élément de la collection
            'entry_options' => [
                'label' => false, // Entité associée à la collection d'étapes
            ],
            'label' => '<i class="bi bi-pencil"></i> Etapes de la recette ', // Ajoutez l'icône au label
            'label_html' => true, // Indique que le label contient du HTML
            'allow_add' => true, // Permettre à l'utilisateur d'ajouter de nouveaux éléments
            'allow_delete' => true, // Permettre à l'utilisateur de supprimer des éléments existants
        ])
        ->add('name', TextType::class, [
            'attr' => ['class' => 'form-control custom-form-control'], // Ajout de la classe Bootstrap
            'label' => '<i class="bi bi-card-text"></i> Nom ', // Ajoutez l'icône au label
            'label_html' => true, // Indique que le label contient du HTML
        ])
        ->add('ingredients', CollectionType::class, [
            'entry_type' => IngredientType::class,
            'entry_options' => [
                'label' => false,
            ],
            'label' => '<i class="bi bi-cart-check"></i> Ingrédients', // Ajoutez l'icône au label
            'label_html' => true, // Indique que le label contient du HTML
            'allow_add' =>true,
            'allow_delete' => true,
        ])
        ->add('pic', FileType::class, [
            'attr' => ['class' => 'form-control form-control custom-file-control'],
            'label' => '<i class="bi bi-image"></i> Image png ou jpg (facultatif) ', // Ajoutez l'icône au label
            'label_html' => true, // Indique que le label contient du HTML
            'data_class' => null,
            'required' => false
        ])
        ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
