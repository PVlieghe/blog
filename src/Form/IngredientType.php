<?php

namespace App\Form;

use App\Entity\Food;
use App\Entity\Ingredient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('food', EntityType::class, [
                'class' => Food::class,
                'choice_label' => 'name',
            ]);

            if (!$options['exclude_quantity']) {
                $builder->add('quantity');
            }
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
            'exclude_quantity' => false, 
        ]);
    }
}
