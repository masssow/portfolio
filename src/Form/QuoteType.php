<?php

namespace App\Form;

use App\Entity\Quote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class QuoteType extends AbstractType
{
    public function __construct(private TranslatorInterface $t) {}

    public function buildForm(FormBuilderInterface $b, array $opt): void
    {
        $packChoices = [
            'web'        => 'services2.cards.web.title',
            'ecommerce'  => 'services2.cards.ecommerce.title',
            'tools'      => 'services2.cards.tools.title',
            'automation' => 'services2.cards.automation.title',
            'terrain'    => 'services2.cards.terrain.title',
            'support'    => 'services2.cards.support.title',
        ];

        $b->add('name', TextType::class, [
            'label' => 'quote.form.name',
            'constraints' => [new Assert\NotBlank()],
        ])
            ->add('company', TextType::class, [
                'label' => 'quote.form.company',
                'required' => false,
            ])
            ->add('phone', TelType::class, [
                'label' => 'quote.form.phone',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('email', EmailType::class, [
                'label' => 'quote.form.email',
                'constraints' => [new Assert\NotBlank(), new Assert\Email(mode: Assert\Email::VALIDATION_MODE_STRICT)],
            ])
            ->add('pack', ChoiceType::class, [
                'label'        => 'quote.form.pack',
                'placeholder'  => 'quote.form.pack.placeholder',
                'choices'      => array_keys($packChoices),
                'choice_label' => fn(string $v) => $this->t->trans($packChoices[$v]),
                'choice_value' => fn(?string $v) => $v,
                'data'         => $opt['selected_pack'] ?? null,
                'required'     => false,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'quote.form.message',
                'attr' => ['rows' => 5],
                'constraints' => [new Assert\NotBlank(), new Assert\Length(min: 12)],
            ])
            // Time-trap
            ->add('render_ts', HiddenType::class, [
                'mapped' => false,
                'data'   => (string)($opt['render_ts'] ?? time()),
            ]);
        // Honeypot
         
    }

    public function configureOptions(OptionsResolver $r): void
    {
        $r->setDefaults([
            'data_class'         => Quote::class,
            'selected_pack'      => null,
            'honeypot_name'      => 'hp_static',
            'render_ts'          => null,
            'translation_domain' => 'messages',
            'csrf_protection'    => true,
            'allow_extra_fields' => true,
        ]);
    }
}
