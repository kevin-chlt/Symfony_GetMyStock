<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Products;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "label"=>'Nom du produit',
                'attr'=> ['class' => 'form-control mb-3'],
                'label_attr'=>['class'=>'mb-1'],
                'error_bubbling' => true,
                ])
            ->add('price', MoneyType::class, [
                "label"=>"Prix HT",
                'attr'=> ['class' => 'form-control mb-3'],
                'label_attr'=>['class'=>'mb-1'],
                'error_bubbling'=> true,
                'invalid_message' => 'Veuillez entrer un nombre avec virgule ou sans'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label'=> 'name',
                'label'=> 'Categorie du produit',
                'attr'=> ['class' => 'form-select mb-3'],
                'label_attr'=>['class'=>'mb-1']
            ])
            ->add('imagePath', FileType::class, [
                'required' => false,
                'label'=> false,
                'error_bubbling' => true,
                'data_class' => null,
                'empty_data' => 'image/default_image.jpg',
                'constraints' => [
                    new Image(
                    [
                        'mimeTypes'=> ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Type d\'image non acceptée, veuillez envoyer une image de type JPEG, PNG, BMP ou GIF',
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Taille de votre fichier supérieur à {{ limit }} Mo'
                    ]
                )]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
