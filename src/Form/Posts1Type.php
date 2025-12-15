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
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class Posts1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', CKeditorType::class, [
            'config_name' => 'default',
            'required' => false,
            ])
            ->add('content', CKEditorType::class)
            ->add('paragraphe2',CKEditorType::class,
            [
                'config_name' => 'default',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Paragraphe surligné'

                ]
            ],
                        
            )
            ->add('paragraphe3', CKEditorType::class, [
            'config_name' => 'default',
            'required' => false,
            ])

            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            ->remove('imageName',TextType::class, [
            'attr' => [
                'class' => 'img-fluid '
            ],
                ])
            ->remove('imageTwoName', TextType::class, [
                'attr' => [
                    'class' => 'img-fluid '
                ],
            ])
            ->remove('imageThreeName', TextType::class, [
                'attr' => [
                    'class' => 'img-fluid '
                ],
            ])
            
            ->remove('imageSize')
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
