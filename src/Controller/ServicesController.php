<?php

namespace App\Controller;

use App\Content\AccompanimentCatalog;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServicesController extends AbstractController
{
    #[Route('/{_locale}/services', name: 'app_services', requirements: ['_locale' => 'fr|es'])]
    public function index(): Response
    {
        return $this->render('services/index.html.twig', [
            'services' => AccompanimentCatalog::SERVICES,
        ]);
    }

    #[Route('/{_locale}/services/{slug}', name: 'app_service_detail',
        requirements: [
            '_locale' => 'fr|es',
            'slug'    => 'sites-web|e-commerce|outils-gestion|reservations|seo-contenu|automatisations|maintenance',
        ]
    )]
    public function detail(string $slug): Response
    {
        $services = AccompanimentCatalog::SERVICES;
        $current  = $services[$slug];
        $others   = array_filter($services, fn($k) => $k !== $slug, ARRAY_FILTER_USE_KEY);

        return $this->render('services/detail.html.twig', [
            'slug'     => $slug,
            'yaml_key' => $current['key'],
            'color'    => $current['color'],
            'others'   => $others,
        ]);
    }
}
