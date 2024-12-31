<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

Class TimeExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('time_ago', [$this, 'timeAgo']),
        ];

    }

    public function timeAgo(\DateTimeInterface $dateTime): string
    {
        $now = new \DateTime();
        $diff = $now->diff($dateTime);


        if ($diff->y > 0) {
            return $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        } elseif ($diff->m > 0) {
            return $diff->m . ' mois';
        } elseif ($diff->d > 0) {
            return $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
        } elseif ($diff->h > 0) {
            return $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
        } elseif ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        } else {
            return 'quelques secondes';
        }

    }

}