<?php

namespace App\Form;

use App\Entity\Posts;
use App\Entity\CategoriePosts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriePosts1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('imageName')
            ->add('imageSize')
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            // ->add('posts', EntityType::class, [
            //     'class' => Posts::class,
            //     'choice_label' => 'title',
            //     'multiple' => true,
            // ])
            ->add('imageFile', VichImageType::class, ["required" => false])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategoriePosts::class,
        ]);
    }
}
