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
                'label' => 'Aliment',
                'row_attr' => ['class' => 'col-6'],
                'attr' => ['class' => 'form-control custom-form-control'] 
            ]);

            if (!$options['exclude_quantity']) {
                $builder->add('quantity', null, [
                    'row_attr' => ['class' => 'col-3'],
                    'attr' => ['class' => 'form-control custom-form-control'],
                    'label' =>'Quantité'
                ]);
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
