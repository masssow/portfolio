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
            'local'  => 'packs.local.title',
            'ecom'   => 'packs.ecom.title',
            'com'    => 'packs.com.title',
            'maint'  => 'packs.maint.title',
            'social' => 'packs.social.title',
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
                'label' => 'quote.form.pack',
                'placeholder' => 'quote.form.pack.placeholder',
                'choices' => array_keys($packChoices),
                'choice_label' => fn(string $v) => $this->t->trans($packChoices[$v]),
                'choice_value' => fn(?string $v) => $v,
                'data' => $opt['selected_pack'] ?? null,
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('budget', ChoiceType::class, [
                'label' => 'quote.form.budget',
                'required' => false,
                'placeholder' => 'quote.form.budget.placeholder',
                'choices' => [
                    'quote.form.budget.1k'  => '≤1k',
                    'quote.form.budget.2k'  => '1–2k',
                    'quote.form.budget.5k'  => '2–5k',
                    'quote.form.budget.5k+' => '5k+',
                ],
                'choice_label' => fn(string $k) => $this->t->trans($k),
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
            ])
            // Honeypot dynamique (nom aléatoire)
            ->add($opt['honeypot_name'] ?? 'hp_fallback', HiddenType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'off', 'class' => 'hp-field'],
            ]);
    }

    public function configureOptions(OptionsResolver $r): void
    {
        $r->setDefaults([
            'data_class' => Quote::class,
            'selected_pack' => null,
            'honeypot_name' => 'hp_fallback',
            'render_ts'     => null,
            'translation_domain' => 'messages',
            'csrf_protection' => true,
            
        ]);
    }
}
