<?php

namespace App\Controller;

use App\Content\AccompanimentCatalog;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SeoController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap', methods: ['GET'])]
    public function sitemap(UrlGeneratorInterface $urls, PostsRepository $posts): Response
    {
        $locations = [];
        $routes = ['app_home', 'app_services', 'app_about', 'app_tarifs', 'app_quote',
            'app_legal_mentions', 'app_legal_privacy', 'app_legal_cookies'];
        foreach (['fr', 'es'] as $locale) {
            foreach ($routes as $route) {
                $locations[] = $urls->generate($route, ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL);
            }
            foreach (AccompanimentCatalog::SERVICES as $slug => $service) {
                $locations[] = $urls->generate('app_service_detail', ['_locale' => $locale, 'slug' => $slug], UrlGeneratorInterface::ABSOLUTE_URL);
            }
        }
        $locations[] = $urls->generate('app_posts', [], UrlGeneratorInterface::ABSOLUTE_URL);
        foreach ($posts->findAll() as $post) {
            $locations[] = $urls->generate('app_single_posts', ['id' => $post->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $this->render('seo/sitemap.xml.twig', ['locations' => $locations], new Response('', 200, ['Content-Type' => 'application/xml; charset=UTF-8']));
    }

    #[Route('/robots.txt', name: 'app_robots', methods: ['GET'])]
    public function robots(UrlGeneratorInterface $urls): Response
    {
        return new Response("User-agent: *\nDisallow: /admin\nDisallow: /login\nSitemap: " . $urls->generate('app_sitemap', [], UrlGeneratorInterface::ABSOLUTE_URL) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
