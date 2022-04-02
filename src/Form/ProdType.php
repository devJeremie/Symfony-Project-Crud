<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ProdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label' =>'Nom du produit',
                'required' =>true,
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'max' => 10,
                        'minMessage' => 'Le nom est top petit ({{ limit }} min)',
                        'maxMessage' => 'Le nom est trop grand ({{ limit}} max)'
                    ])
                ]
            ])
            ->add('description',TextareaType::class,[
                'label' => 'Description du produit'
            ])
            ->add('price',IntegerType::class,[
                'label' => 'Prix du produit'
            ])
            ->add('stock',IntegerType::class,[
                'label' => 'Quantité en stock'
            ])
            ->add('img', FileType::class,[
                'label' => 'Image du produit'
            ])
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
