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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

final class QuoteController extends AbstractController
{
    private const ADMIN_EMAIL = 'contact.massgrafik@gmail.com';
    private const ADMIN_NAME  = 'MassGrafik';

    public function __construct(
        private RateLimiterFactory $quoteFormLimiter,
        private TranslatorInterface $t,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
    ) {}


    #[Route('/{_locale}/devis', name: 'app_quote', requirements: ['_locale' => 'fr|es'], methods: ['GET', 'POST'])]
    public function request(Request $req): Response
    {
        $session = $req->getSession();

        // GET : préparer honeypot & timer
        if ($req->isMethod('GET')) {
            $session->set('quote_started_at', time());
            $hp = 'hp_' . bin2hex(random_bytes(4));     // nom de champ aléatoire
            $session->set('quote_hp_name', $hp);
        }

        $selectedPack = $req->query->get('pack') ?? $req->query->get('service');
        $honeypotName = $session->get('quote_hp_name', 'hp_fallback');

        // IMPORTANT : ne pas ajouter le honeypot via le FormType
        $form = $this->createForm(QuoteType::class, null, [
            'selected_pack' => $selectedPack,
            'honeypot_name' => $honeypotName,
            'render_ts'     => time(),
        ]);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            // 1) Rate limiting par IP
            $limiter = $this->quoteFormLimiter->create($req->getClientIp() ?? 'anon');
            if (!$limiter->consume(1)->isAccepted()) {
                $this->addFlash('warning', $this->t->trans('quote.flash.too_many'));
                return $this->redirectToRoute('app_quote', ['_locale' => $req->getLocale()]);
            }

            // 2) Honeypot dynamique (champ HTML brut caché)
            $formName = $form->getName();
            // OK: all() renvoie un array, pas de default passé à get()
            $posted   = $req->request->all($formName);
            $hpVal    = is_array($posted) ? ($posted[$honeypotName] ?? '') : '';

            if ($hpVal !== '') {
                $this->logger->info('Honeypot triggered', ['ip' => $req->getClientIp()]);
                $this->addFlash('success', $this->t->trans('quote.flash.ok'));
                return $this->redirectToRoute('app_quote_thanks', ['_locale' => $req->getLocale()]);
            }

            // 3) Time-trap (>= 5s)
            $startedAt = (int) $session->get('quote_started_at', time());
            if ((time() - $startedAt) < 5) {
                $this->addFlash('warning', $this->t->trans('quote.flash.too_fast'));
                return $this->redirectToRoute('app_quote', ['_locale' => $req->getLocale(), 'pack' => $selectedPack]);
            }

            // 4) Filtrage contenu basique (UTILISER le form, pas $data)
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

            // 5) Vérif MX basique (depuis le form)
            $email = (string) $form->get('email')->getData();
            if ($email && str_contains($email, '@')) {
                $domain = substr(strrchr($email, '@'), 1);
                if ($domain && !@checkdnsrr($domain, 'MX')) {
                    $this->addFlash('warning', $this->t->trans('quote.flash.bad_mx'));
                    return $this->redirectToRoute('app_quote', ['_locale' => $req->getLocale(), 'pack' => $selectedPack]);
                }
            }

            // 6) Traitement “propre”
            if ($form->isValid()) {
                /** @var Quote $entity */
                $entity = $form->getData();
                $entity
                    ->setLocale($req->getLocale())
                    ->setIp($req->getClientIp() ?? 'unknown')
                    ->setUserAgent($req->headers->get('User-Agent'))
                    ->setCreatedAt(new \DateTimeImmutable('now'));
                if (method_exists($entity, 'getStatus') && !$entity->getStatus()) {
                    $entity->setStatus('new');
                }

                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $this->sendAdminNotification($entity);
                $this->sendUserConfirmation($entity);

                return $this->redirectToRoute('app_quote_thanks', ['_locale' => $req->getLocale()]);
            }
            // Formulaire invalide : log les erreurs et ré-affiche avec les messages sur les champs
            foreach ($form->getErrors(true) as $error) {
                $this->logger->warning('Quote form error: ' . $error->getMessage(), [
                    'field' => $error->getOrigin()?->getName(),
                ]);
            }
        }

        return $this->render('quote/request.html.twig', [
            'form'           => $form->createView(),
            'selected_pack'  => $selectedPack,
            'honeypot_name'  => $honeypotName,
        ]);
    }


    private function sendAdminNotification(Quote $quote): void
    {
        try {
            $html = $this->renderView('emails/quote_admin.html.twig', ['quote' => $quote]);
            $email = (new Email())
                ->from(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
                ->to(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
                ->replyTo(new Address($quote->getEmail(), $quote->getName()))
                ->subject('Nouvelle demande de devis — ' . $quote->getName())
                ->html($html);
            $this->mailer->send($email);
        } catch (\Throwable $e) {
            $this->logger->error('Admin email failed: ' . $e->getMessage());
        }
    }

    private function sendUserConfirmation(Quote $quote): void
    {
        try {
            $html = $this->renderView('emails/quote_user.html.twig', ['quote' => $quote]);
            $email = (new Email())
                ->from(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
                ->to(new Address($quote->getEmail(), $quote->getName()))
                ->subject('Votre demande a bien été reçue — MassGrafik')
                ->html($html);
            $this->mailer->send($email);
        } catch (\Throwable $e) {
            $this->logger->error('User confirmation email failed: ' . $e->getMessage());
        }
    }

    #[Route('/devis', name: 'app_quote_nolocale', methods: ['GET'])]
    public function redirectToLocalized(Request $req): Response
    {
        // Choix de la locale préférée parmi fr|es
        $locale = $req->getPreferredLanguage(['fr', 'es']) ?? 'fr';

        return $this->redirectToRoute('app_quote', ['_locale' => $locale], 302);
    }


    #[Route('/thank', name: 'app_quote_thanks', requirements: ['_locale' => 'fr|es'], methods: ['GET', 'POST'])]
    public function thanks(Request $req): Response {

            return $this->render('quote/thanks.html.twig',[

            ]);
    }
}
