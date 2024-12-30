<?php

namespace App\Form;

use App\Entity\Posts;
use App\Entity\CategoriePosts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class Posts1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('paragraphe2', TextType::class,[
                'attr' => [
                        'placeholder' => 'Paragraphe surligné'

                ]
            ]
                        
            )
            ->add('paragraphe3')

            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('imageName',TextType::class, [
            'attr' => [
                'class' => 'img-fluid '
            ],
                ])
            ->add('imageTwoName', TextType::class, [
                'attr' => [
                    'class' => 'img-fluid '
                ],
            ])
            ->add('imageThreeName', TextType::class, [
                'attr' => [
                    'class' => 'img-fluid '
                ],
            ])
            
            ->add('imageSize')
            ->add('categoriePosts', EntityType::class, [
                'class' => CategoriePosts::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('imageFile', VichImageType::class,[
                'attr' => [
                    'class' => 'form-select'
                ]
                ],
            
                    ["required" => false])
            ->add('imageTwoFile', VichImageType::class,[
                'attr' => [
                    'class' => 'form-select'
            ]
                ],
                    ["required" => false])
            ->add('imageThreeFile', VichImageType::class,[
                'attr' => [
                    'class' => 'form-select'
            ]
                ],
                    ["required" => false])
            ->add('submit', SubmitType::class, [
                    'label' => 'Save', // Texte du bouton
                        'attr' => [
                            'class' => 'btn btn-primary mt-3', // Style Bootstrap pour le bouton
            ],
        ])


            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
            'attr' => [
                'class'=> 'form-group'
            ]
        ]);
    }
}
