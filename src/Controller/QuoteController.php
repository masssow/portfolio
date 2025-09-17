<?php
// src/Controller/QuoteController.php
namespace App\Controller;

use App\Form\QuoteType;
use App\Entity\Quote;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class QuoteController extends AbstractController
{
    public function __construct(
        private RateLimiterFactory $quoteFormLimiter,
        private TranslatorInterface $t,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager

    ) {}

    #[Route('/{_locale}/devis', name: 'app_quote', requirements: ['_locale' => 'fr|es'], methods: ['GET', 'POST'])]
    public function request(Request $req): Response
    {
        $session = $req->getSession();

        // ——— GET : préparer honeypot & timer ———
        if ($req->isMethod('GET')) {
            $session->set('quote_started_at', time());
            $hp = 'hp_' . bin2hex(random_bytes(4));         // nom de champ aléatoire
            $session->set('quote_hp_name', $hp);
        }

        $selectedPack = $req->query->get('pack'); // local|ecom|com|maint|social
        $honeypotName = $session->get('quote_hp_name', 'hp_fallback');

        $form = $this->createForm(QuoteType::class, null, [
            'selected_pack' => $selectedPack,
            'honeypot_name' => $honeypotName,
            'render_ts'     => time(),
        ]);
        $form->handleRequest($req);

        // ——— POST : contrôles anti-spam ———
        if ($form->isSubmitted()) {
            // 1) Rate limiting par IP
            $limiter = $this->quoteFormLimiter->create($req->getClientIp() ?? 'anon');
            if (!$limiter->consume(1)->isAccepted()) {
                $this->addFlash('warning', $this->t->trans('quote.flash.too_many'));
                return $this->redirectToRoute('app_quote', ['_locale' => $req->getLocale()]);
            }

            // 2) Honeypot dynamique
            $hpVal = $req->request->all('quote')[$honeypotName] ?? '';
            if (!empty($hpVal)) {
                $this->logger->info('Honeypot triggered', ['ip' => $req->getClientIp()]);
                // on fait comme si tout allait bien
                $this->addFlash('success', $this->t->trans('quote.flash.ok'));
                return $this->redirectToRoute('app_quote_thanks', ['_locale' => $req->getLocale()]);
            }

            // 3) Time-trap (>= 5s)
            $startedAt = (int)$session->get('quote_started_at', time());
            if ((time() - $startedAt) < 5) {
                $this->addFlash('warning', $this->t->trans('quote.flash.too_fast'));
                return $this->redirectToRoute('app_quote', ['_locale' => $req->getLocale(), 'pack' => $selectedPack]);
            }

            // 4) Filtrage contenu basique
            $message = (string) $form->get('message')->getData();
            if (mb_strlen($message) < 12) {
                $this->addFlash('warning', $this->t->trans('quote.flash.too_short'));
                return $this->redirectToRoute('app_quote', ['_locale' => $req->getLocale(), 'pack' => $selectedPack]);
            }
            preg_match_all('#https?://#i', $message, $m);
            if (count($m[0] ?? []) > 2) {
                $this->addFlash('warning', $this->t->trans('quote.flash.too_many_links'));
                return $this->redirectToRoute('app_quote', ['_locale' => $req->getLocale(), 'pack' => $selectedPack]);
            }

            // 5) (Optionnel) Vérif MX basique
            $email = (string)($data['email'] ?? '');
            if ($email && str_contains($email, '@')) {
                $domain = substr(strrchr($email, '@'), 1);
                if ($domain && !checkdnsrr($domain, 'MX')) {
                    $this->addFlash('warning', $this->t->trans('quote.flash.bad_mx'));
                    return $this->redirectToRoute('app_quote', ['_locale' => $req->getLocale(), 'pack' => $selectedPack]);
                }
            }

            // 6) Traitement “propre” (TODO: envoi e-mail / stockage)
            if ($form->isValid()) {

                /** @var Quote $entity */
                $entity = $form->getData();
                $entity
                    ->setLocale($req->getLocale())
                    ->setIp($req->getClientIp() ?: null)
                    ->setUserAgent($req->headers->get('User-Agent'))
                    ->setCreatedAt(new \DateTimeImmutable('now'));
                if (method_exists($entity, 'getStatus') && !$entity->getStatus()) {
                    $entity->setStatus('new');
                }
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                $this->addFlash('success', $this->t->trans('quote.flash.ok'));
                return $this->redirectToRoute('app_quote_thanks', ['_locale' => $req->getLocale()]);


              

                
            }
        }

        return $this->render('quote/request.html.twig', [
            'form'           => $form->createView(),
            'selected_pack'  => $selectedPack,
            'honeypot_name'  => $honeypotName,
        ]);
    }




    #[Route('/thank', name: 'app_quote_thanks', requirements: ['_locale' => 'fr|es'], methods: ['GET', 'POST'])]
    public function thanks(Request $req): Response {

            return $this->render('quote/thanks.html.twig',[

            ]);
    }
}
