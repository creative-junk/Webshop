<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuctionProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product',null,[
                'placeholder'=>'Choose a Product'
            ])
            ->add('quantity',null,array(
                        'label_format' => 'Quantity in Bundle',
                    ))
            ->add('currency',ChoiceType::class,array(
                'choices' => array(
                    'US Dollars - $'=>'$',
                    'Kenya Shillings - Ksh'=>'Ksh',
                    'Euros - Eur'=>'Eur',
                ),
            ))
            ->add('bundlePrice')
            ->add('finalPrice');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Auction'
        ]);
    }

    public function getName()
    {
        return 'app_bundle_auction_product_form';
    }
}
