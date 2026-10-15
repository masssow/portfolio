<?php

namespace App\Tests\Controller;

use App\Content\AccompanimentCatalog;
use App\Entity\Quote;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

final class PresentationTest extends WebTestCase
{
    public function testPublicPagesHaveLocalizedContentAndValidMetadata(): void
    {
        $client = static::createClient();
        foreach (['fr', 'es'] as $locale) {
            $paths = ['', '/services', '/tarifs', '/a-propos', '/devis', '/merci', '/mentions-legales', '/confidentialite', '/cookies'];
            foreach (AccompanimentCatalog::SERVICES as $slug => $service) {
                $paths[] = '/services/' . $slug;
            }
            foreach ($paths as $path) {
                $crawler = $client->request('GET', '/' . $locale . $path);
                self::assertResponseIsSuccessful();
                self::assertSelectorExists('html[lang="' . $locale . '"]');
                self::assertCount(1, $crawler->filter('main h1'), $locale . $path);
                self::assertCount(1, $crawler->filter('link[rel="canonical"]'));
                self::assertCount(3, $crawler->filter('link[hreflang]'));
                self::assertNotSame('', $crawler->filter('meta[name="description"]')->attr('content'));
                self::assertStringNotContainsString('services2.', $crawler->filter('main')->text());
                self::assertStringNotContainsString('home.', $crawler->filter('main')->text());
                foreach ($crawler->filter('script[type="application/ld+json"]') as $schema) {
                    self::assertIsArray(json_decode($schema->textContent, true, 512, JSON_THROW_ON_ERROR));
                }
            }
        }
    }

    public function testQuoteSelectionsAndLanguageLinks(): void
    {
        $client = static::createClient();
        foreach (['fr', 'es'] as $locale) {
            foreach (array_merge(AccompanimentCatalog::PACKS, array_keys(AccompanimentCatalog::SERVICES), ['unknown']) as $value) {
                $crawler = $client->request('GET', '/' . $locale . '/devis?service=' . $value);
                self::assertResponseIsSuccessful();
                $selected = $crawler->filter('#quoteForm')->form();
                self::assertSame(AccompanimentCatalog::normalize($value) ?? '', $selected['quote[pack]']->getValue());
                self::assertStringNotContainsString('service=', $crawler->filter('link[rel="canonical"]')->attr('href'));
            }
        }
        $client->request('GET', '/contact');
        self::assertResponseRedirects('/fr/devis', 301);
        $client->request('GET', '/thank');
        self::assertResponseRedirects('/fr/merci', 301);
    }

    public function testConfirmationEmailUsesTheRequestLanguageAndEscapesContent(): void
    {
        static::bootKernel();
        $twig = static::getContainer()->get(Environment::class);
        $quote = (new Quote())->setName('<Samba>')->setMessage('<script>alert(1)</script>')->setPack('starter');
        $html = $twig->render('emails/quote_user.html.twig', ['quote' => $quote, 'locale' => 'es', 'choice_key' => 'offers_cards.starter.name']);
        self::assertStringContainsString('lang="es"', $html);
        self::assertStringContainsString('Hola &lt;Samba&gt;', $html);
        self::assertStringContainsString('Starter Digital', $html);
        self::assertStringNotContainsString('<script>', $html);
        self::assertStringNotContainsString('48', $html);
    }

    public function testSitemapContainsBothLanguagesAndOnlyRealArticleUrls(): void
    {
        $client = static::createClient();
        $posts = $this->createMock(PostsRepository::class);
        $posts->method('findAll')->willReturn([]);
        static::getContainer()->set(PostsRepository::class, $posts);
        $client->request('GET', '/sitemap.xml');
        self::assertResponseIsSuccessful();
        $xml = simplexml_load_string($client->getResponse()->getContent());
        self::assertNotFalse($xml);
        self::assertCount(31, $xml->url);
        self::assertStringNotContainsString('/merci', $client->getResponse()->getContent());
        $client->request('GET', '/robots.txt');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('/sitemap.xml', $client->getResponse()->getContent());
    }

    public function testTranslationCataloguesHaveTheSameKeys(): void
    {
        $flatten = function (array $data, string $prefix = '') use (&$flatten): array {
            $keys = [];
            foreach ($data as $key => $value) {
                $name = $prefix . $key;
                $keys = array_merge($keys, is_array($value) ? $flatten($value, $name . '.') : [$name]);
            }
            sort($keys);
            return $keys;
        };
        $root = dirname(__DIR__, 2);
        self::assertSame($flatten(Yaml::parseFile($root . '/translations/messages.fr.yaml')), $flatten(Yaml::parseFile($root . '/translations/messages.es.yaml')));
    }

    public function testFrenchBlogLayoutKeepsPaginationAndDoesNotInventTranslations(): void
    {
        static::bootKernel();
        $request = Request::create('/blog?page=2');
        $request->attributes->set('_route', 'app_posts');
        $request->attributes->set('_route_params', []);
        $request->setLocale('fr');
        $stack = static::getContainer()->get(RequestStack::class);
        $stack->push($request);
        try {
            $twig = static::getContainer()->get(Environment::class);
            $html = $twig->createTemplate("{% extends 'blog-base.html.twig' %}{% block body %}<h1>Ressources</h1>{% endblock %}")->render();
            self::assertStringContainsString('/blog?page=2', $html);
            self::assertStringContainsString('Samba Sow', $html);
            self::assertStringContainsString('href="/es"', $html);
            self::assertStringNotContainsString('rel="alternate"', $html);
            self::assertStringNotContainsString('Sénégal', $html);
        } finally {
            $stack->pop();
        }
    }
}
