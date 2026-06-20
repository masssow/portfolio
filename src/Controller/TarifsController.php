<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TarifsController extends AbstractController
{
    #[Route('/{_locale}/tarifs', name: 'app_tarifs', requirements: ['_locale' => 'fr|es'])]
    public function index(): Response
    {
        return $this->render('tarifs/index.html.twig');
    }
}
