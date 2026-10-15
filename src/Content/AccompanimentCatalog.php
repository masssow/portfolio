<?php

namespace App\Content;

/** Shared identifiers for service pages and quote preselection. */
final class AccompanimentCatalog
{
    public const SERVICES = [
        'sites-web' => ['key' => 'web', 'color' => 'teal', 'group' => 'visibility'],
        'e-commerce' => ['key' => 'ecommerce', 'color' => 'purple', 'group' => 'visibility'],
        'seo-contenu' => ['key' => 'content_seo', 'color' => 'green', 'group' => 'visibility'],
        'outils-gestion' => ['key' => 'tools', 'color' => 'coral', 'group' => 'organisation'],
        'reservations' => ['key' => 'booking', 'color' => 'gold', 'group' => 'organisation'],
        'automatisations' => ['key' => 'automation', 'color' => 'blue', 'group' => 'organisation'],
        'maintenance' => ['key' => 'operations', 'color' => 'teal', 'group' => 'evolution'],
    ];

    public const PACKS = ['starter', 'pro', 'business'];

    public static function choices(): array
    {
        $choices = [];
        foreach (self::PACKS as $pack) {
            $choices[$pack] = 'offers_cards.' . $pack . '.name';
        }
        foreach (self::SERVICES as $service) {
            $choices[$service['key']] = 'services2.cards.' . $service['key'] . '.title';
        }
        // Existing quote values remain valid for older links and records.
        $choices['terrain'] = 'services2.cards.terrain.title';
        $choices['support'] = 'services2.cards.support.title';

        return $choices;
    }

    public static function normalize(?string $value): ?string
    {
        $value = self::SERVICES[$value ?? '']['key'] ?? $value;

        return $value !== null && isset(self::choices()[$value]) ? $value : null;
    }
}
