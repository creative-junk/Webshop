<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('slug')
            ->add('summary')
            ->add('description')
            ->add('price',NumberType::class)
            ->add('category',null,[
                'placeholder'=>'Choose a Category'
            ])
            ->add('isAuthorized',ChoiceType::class,[
                'choices'=>[
                    'Yes'=>true,
                    'No'=>false,
                ]
            ])
            ->add('isActive',ChoiceType::class,[
                'choices'=>[
                    'Yes'=>true,
                    'No'=>false,
                ]
            ])
            ->add('createdAt',DateType::class,[
                'widget'=>'single_text',
                'attr' => [
                    'class' => 'js-datepicker'
                ],
                'html5'=>false,
            ])
            ->add('updatedAt');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Product'
        ]);
    }

    public function getName()
    {
        return 'app_bundle_product_form_type';
    }
}
