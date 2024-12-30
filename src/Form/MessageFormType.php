<?php

namespace App\Form;

use App\Entity\Posts;
use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false, // Supprime le label pour correspondre à l'exemple
                'attr' => [
                    'class' => 'form-control shadow-sm',
                    'id' => 'name',
                    'placeholder' => 'Votre Nom',
                    'onfocus' => "this.placeholder = ''",
                    'onblur' => "this.placeholder = 'Entrez votre Nom'",
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control shadow-sm',
                    'id' => 'email',
                    'placeholder' => 'Votre adresse mail',
                    'onfocus' => "this.placeholder = ''",
                    'onblur' => "this.placeholder = 'Entrez votre adresse mail'",
                ],
            ])
            ->add('subject', TextType::class, [
                'label' => false,
                'mapped' => false, // Si le champ n'existe pas dans l'entité Messages
                'attr' => [
                    'class' => 'form-control shadow-sm',
                    'id' => 'subject',
                    'placeholder' => 'Objet',
                    'onfocus' => "this.placeholder = ''",
                    'onblur' => "this.placeholder = 'Subject'",
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control shadow-sm',
                    'rows' => 5,
                    'placeholder' => 'Message',
                    'onfocus' => "this.placeholder = ''",
                    'onblur' => "this.placeholder = 'Message'",
                    'required' => '',
                ],
            ])

            ->add('honeypot_field', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'style' => 'display:none;',
                    'autocomplete' => 'off',
                    ],
                ])

                ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class'     => 'primary-btn primary_btn',
                        ],
                ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'attr' => [
               'class' => 'comment-form ',
                'id' => 'comment-form',
                'method'    =>  'post',
                'action'    =>   ''
            ]
        ]);
    }
}
