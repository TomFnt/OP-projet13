<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $quantityChoices = range($options['data']['minQuantity'], $options['data']['maxQuantity']);
        $choices = array_combine($quantityChoices, $quantityChoices);

        $builder
            ->add('quantity', ChoiceType::class, [
                'label' => 'Quantité',
                'choices' => $choices,
                'data' => $options['data']['defaultQuantity'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['defaultQuantity' => 1,
            'minQuantity' => 1,
            'maxQuantity' => 10]);
    }
}
