<?php

namespace App\Form\Type;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\VatClass;

class ProductType extends AbstractType {

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('barcode', TextType::class)
                ->add('name', TextType::class)
                ->add('cost', NumberType::class)
                ->add('vatClass', EntityType::class, array(
                    'class' => VatClass::class
                ))
        ;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => Product::class,
        ));
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix() {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getName() {
        return '';
    }

}
