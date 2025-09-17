<?php
// src/Controller/LegalController.php
namespace App\Controller;

use App\Entity\ConsentLog;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response, Cookie};

final class LegalController extends AbstractController
{
    #[Route('/{_locale}/mentions-legales', name: 'app_legal_mentions', requirements: ['_locale' => 'fr|es'], methods: ['GET'])]
    public function mentions(): Response
    {
        return $this->render('legal/mentions.html.twig');
    }

    #[Route('/{_locale}/confidentialite', name: 'app_legal_privacy', requirements: ['_locale' => 'fr|es'], methods: ['GET'])]
    public function privacy(): Response
    {
        return $this->render('legal/privacy.html.twig');
    }

    #[Route('/{_locale}/cookies', name: 'app_legal_cookies', requirements: ['_locale' => 'fr|es'], methods: ['GET'])]
    public function cookies(): Response
    {
        return $this->render('legal/cookies.html.twig');
    }

     #[Route('/{_locale}/consent', name: 'app_consent_log', requirements: ['_locale'=>'fr|es'], methods:['POST'])]
    public function log(Request $req, EntityManagerInterface $em): Response
    {
        $data = json_decode($req->getContent(), true) ?? [];
        $cid  = $data['consentId'] ?? bin2hex(random_bytes(16));

        $log = (new ConsentLog());
        $log->setConsentId($cid);
        $log->setStatus($data['status'] ?? 'accepted');
        $log->setCategories($data['categories'] ?? []);
        $log->setLocale($req->getLocale());
        $ip = $req->getClientIp();
        $salt = $_ENV['CONSENT_SALT'] ?? 'change-me';
        $log->setIpHash($ip ? hash('sha256', $ip.$salt) : null);
        $log->setUserAgent($req->headers->get('User-Agent'));
        $log->setPolicyVersion('v1');
        $log->setCreatedAt(new \DateTimeImmutable('now'));

        $em->persist($log);
        $em->flush();

        $cookie = Cookie::create('consent_id', $cid, (new \DateTime('+13 months')))
            ->withSecure(true)->withHttpOnly(false)->withPath('/');

        $resp = new JsonResponse(['ok'=>true,'consentId'=>$cid]);
        $resp->headers->setCookie($cookie);
        return $resp;
    }
}
