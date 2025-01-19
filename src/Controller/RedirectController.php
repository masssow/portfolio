<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RedirectController extends AbstractController
{
    private string $defaultLocale;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(string $defaultLocale = 'fr')
    {
        $this->defaultLocale = $defaultLocale;
    }

    #[Route('/', name: 'default_redirect')]
    public function redirectToDefaultLocale(): RedirectResponse
    {
        return $this->redirectToRoute('app_home', ['_locale' => 'fr']);
    }
}
