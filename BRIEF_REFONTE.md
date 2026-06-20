# MassGrafik — Brief de refonte architecture & contenu

> Fichier de référence à donner à Claude Code (VS Code) pour piloter la refonte
> du site Symfony MassGrafik. Contient l'arborescence cible, l'organisation de
> conception, les contraintes techniques et le contenu déjà validé à réutiliser.

---

## 0. Contexte projet (à ne jamais perdre de vue)

- **Stack** : Symfony 6.4, Twig, SCSS (pas de Tailwind), Bootstrap grille uniquement, Webpack Encore, JS vanilla
- **Multilingue** : FR/ES via fichiers YAML (`translations/messages.fr.yaml`, `messages.es.yaml`), routing `_locale`-aware, appels `trans()` dans Twig
- **Positionnement business** : agence digitale opérée par Samba, basée à Paris, ciblant la diaspora sénégalaise (Paris + Espagne) et les PME locales. Statut auto-entrepreneur en France. Structure SUARL en cours de formalisation au Sénégal (stratégie d'ancienneté pour les marchés publics ARMP).
- **Zone géographique à afficher** : Paris (France), Espagne, Sénégal — toujours ces trois, jamais "international" vague.
- **Cible client** : artisans, petites PME, commerçants, associations, indépendants — pas de profil technique. Le ton doit rester simple, humain, jamais jargonneux, mais détaillé pour éviter l'effet générique.
- **Charte visuelle de référence** : voir section 5. Le design des cartes d'offres (`_offer_cards.scss`) est la référence absolue à appliquer partout.

---

## 1. Diagnostic — état actuel du site live (massgrafik.com)

Problèmes identifiés à corriger en priorité :

1. **Clé de traduction cassée** : `services2.cards.ai.title` et `services2.cards.ai.teaser` s'affichent en clair sur `/services` (bug visible publiquement)
2. **Liens sociaux non fonctionnels** : icônes de partage pointent vers des URLs littérales non remplacées (`URL`, `TITLE`, `#`) sur le blog
3. **Page Services = une seule URL avec ancres** pour 14 catégories → mauvais pour le SEO, aucune page individuelle indexable
4. **Pas de page Tarifs dédiée** → les 3 packs n'existent que sur l'accueil, pas d'URL partageable
5. **Blog mal positionné** : titre d'onglet "Blog Diamono Tech" (incohérent avec la marque), catégories techniques (Programmation, WordPress & CMS, Métiers IT) qui parlent à des développeurs et pas à la cible réelle (PME/artisans)
6. **Bio auteur trop personnelle/informelle** pour la crédibilité "agence" recherchée (utile pour les futurs appels d'offres)
7. **Incohérence géographique** : le site live mentionne "Espagne et international" sur l'accueil, à clarifier en "Paris, Espagne, Sénégal"

---

## 2. Arborescence cible

```
/                                  → Accueil
/services                         → Page index des services (cartes résumé, liens vers pages détail)
/services/sites-web               → Page détail : Sites et présence digitale
/services/outils-gestion          → Page détail : Outils de gestion sur mesure
/services/e-commerce              → Page détail : Boutiques en ligne et paiements
/services/reservations            → Page détail : Réservations et formulaires
/services/seo-contenu             → Page détail : Contenus, SEO et visibilité
/services/automatisations         → Page détail : Automatisations utiles
/services/maintenance             → Page détail : Suivi et exploitation continue
/tarifs                           → Page dédiée : 3 cartes offres + comparatif + FAQ prix
/a-propos                         → Page À propos (philosophie, valeurs, pour qui, zones détaillées)
/ressources                       → Ex-blog, repositionné contenu business/conseil
/ressources/categorie/{slug}      → Catégories repensées (voir section 4.5)
/ressources/{slug}                → Articles individuels
/devis                            → Formulaire de contact unique, pré-rempli selon le pack/service d'origine
/mentions-legales                 → Inchangé
/confidentialite                  → Inchangé
/cookies                          → Inchangé
```

Chaque route existe en double via `_locale` (`/fr/...` et `/es/...` ou équivalent du routing existant).

---

## 3. Organisation de la conception — ordre de mise en œuvre

Travailler dans cet ordre, valider chaque étape avant de passer à la suivante :

### Étape 1 — Corrections urgentes (à faire en premier, indépendamment du reste)
- Corriger ou retirer la clé `services2.cards.ai.title` / `.teaser`
- Corriger les liens sociaux cassés (remplacer `URL`/`TITLE` par les vraies variables Twig)
- Renommer le titre d'onglet du blog ("Blog Diamono Tech" → "Ressources MassGrafik" ou équivalent)

### Étape 2 — Accueil + Tarifs
- Refondre le contenu de l'accueil (textes déjà rédigés et validés, voir section 4.1)
- Créer la nouvelle page `/tarifs` avec les 3 cartes offres (déjà construites en Twig/SCSS, voir section 5), un tableau comparatif, et une FAQ prix
- Retirer les 3 packs de l'accueil OU les garder en résumé avec lien "Voir tous les tarifs" vers `/tarifs`

### Étape 3 — Pages services individuelles (x7)
- Créer les 7 pages détail listées en section 2
- Chaque page reprend la structure : accroche → description longue → liste de points inclus → CTA devis pré-rempli → 2-3 liens vers services connexes (maillage interne)
- La page `/services` (index) devient une grille de 7 cartes résumé pointant vers les pages détail (cartes claires avec accent couleur, sans icône Font Awesome — cohérent avec la charte des offres)

### Étape 4 — À propos
- Appliquer le contenu déjà rédigé (section 4.3)
- Ajouter une section "Zones d'intervention" détaillée (Paris/Île-de-France, Espagne, Sénégal — un paragraphe par zone si possible, ou un bloc à 3 colonnes)

### Étape 5 — Ressources (ex-blog)
- Renommer la section, changer les catégories (section 4.5)
- Rédiger une bio auteur professionnelle de remplacement
- Établir un calendrier éditorial de contenu business (pas technique)

### Étape 6 — Couche SEO technique (en dernier, sur l'ensemble du site)
- Title + meta description uniques par page
- Un seul H1 par page, structure Hn cohérente
- Maillage interne vérifié entre toutes les pages
- URLs slugs propres en FR et ES (distincts, pas de simple `?_locale=es`)
- Données structurées schema.org (`LocalBusiness`, `Service`) sur les pages clés

---

## 4. Contenu déjà rédigé et validé — à réutiliser tel quel

> Ce contenu a été validé avec le client. Claude Code doit l'intégrer directement
> dans les fichiers YAML de traduction, sans le réécrire, sauf demande explicite.

### 4.1 Accueil

Voir fichier `messages_fr_refonte.yaml` / `messages_es_refonte.yaml` (déjà livrés
précédemment) — sections `home.hero`, `home.mirror`, `home.solutions`,
`home.method`, `home.zones`, `home.cta_center`.

Résumé du H1 retenu :
> FR : "On construit votre présence en ligne, vous gérez votre activité"
> ES : "Construimos tu presencia online, tú gestionas tu actividad"

### 4.2 Services (contenu des 7 catégories prioritaires)

Voir fichier `messages_fr_refonte.yaml` / `messages_es_refonte.yaml`, section
`services2.cards.*` — contient déjà : `web`, `tools`, `ecommerce`, `booking`,
`content_seo`, `automation`, `operations`.

**Action pour Claude Code** : éclater ces 7 entrées en 7 templates de page
individuels (`templates/services/sites_web.html.twig`, etc.), chacun reprenant
la clé `desc` (description longue) en plus du `teaser` déjà utilisé sur les
cartes résumé.

Les 7 autres catégories du site actuel (conseil/stratégie pur, identité/design
pur, acquisition payante, dons/mécénat, accessibilité avancée, data/CRM lourd,
IA, production créative) sont retirées de la navigation mais **pas supprimées**
des fichiers YAML — les commenter ou les déplacer dans une clé
`services2.cards_archive.*` pour réactivation future.

### 4.3 À propos

Voir fichier `messages_fr_refonte.yaml` / `messages_es_refonte.yaml`, sections
`about.hero`, `about.philosophy`, `about.values`, `about.forwho`, `about.cta`.

### 4.4 Tarifs (page dédiée à créer)

Contenu déjà existant dans le composant `offer_cards.html.twig` et les clés
`offers_cards.*` (déjà livrés). Pour la nouvelle page `/tarifs`, ajouter :

**Tableau comparatif** — colonnes Starter / Pro PME / Business Tool, lignes :
nombre de pages, outil de gestion inclus (oui/non), espace client, formation,
délai de livraison, maintenance incluse (oui/non/en option).

**FAQ prix à rédiger** (questions types à couvrir) :
- "Le prix affiché est-il vraiment tout compris ?"
- "Que se passe-t-il si mon besoin dépasse le pack choisi ?"
- "Proposez-vous des facilités de paiement ?"
- "La maintenance est-elle obligatoire ?"
- "Travaillez-vous avec des associations à budget limité ?"

### 4.5 Ressources (ex-blog) — nouvelles catégories proposées

Remplacer les catégories techniques actuelles par :

1. **Visibilité & SEO** (remplace "Programmation")
   Conseils pour être trouvé sur Google, fiche Google Business, avis clients
2. **Vendre en ligne** (remplace "WordPress & CMS")
   E-commerce, paiement, gestion de commandes pour non-techniques
3. **Organisation & outils** (remplace "Métiers IT")
   Gagner du temps, centraliser ses demandes clients, automatiser le répétitif
4. **Études de cas** (nouvelle catégorie)
   Cas concrets de clients accompagnés, avant/après, résultats mesurables

**Bio auteur de remplacement (à rédiger par Claude Code ou Claude.ai) :**
Doit transmettre : fondateur de MassGrafik, expertise technique réelle,
approche orientée résultat client, sans familiarité excessive. Éviter les
formulations type "autodidacte dans l'âme" / "un brin de folie" qui
affaiblissent la crédibilité institutionnelle recherchée pour les futurs
appels d'offres publics.

---

## 5. Charte visuelle — référence absolue

Le style des cartes d'offres (déjà livré en Twig + SCSS) est la référence
pour TOUTES les cartes du site, y compris les nouvelles cartes services.

```
Border-radius cartes       : 14px
Border                     : 0.5px solid rgba(0,0,0,0.10)
Padding interne            : 24px
Hover                      : box-shadow 0 8px 32px rgba(0,0,0,0.08) + translateY(-2px)
                              transition 0.18s ease
Typographie titres         : font-weight 500
Typographie descriptions   : 13px, couleur secondaire #5a5a56
Icônes                     : AUCUNE icône Font Aweso­me dans les cartes —
                              utiliser des badges pill colorés à la place
Texte                      : jamais de tout-caps dans les cartes
Boutons CTA                : border 1px solid, border-radius 8px,
                              13px font-weight 500, jamais de tout-caps

Palette accents (badges, bordures, highlights) :
  teal    → #1D9E75 (fond léger #E1F5EE)
  purple  → #534AB7 (fond léger #EEEDFE)
  coral   → #993C1D (fond léger #FAECE7)

Dark mode :
  fond carte    : #1e1e1c
  texte         : #e8e6de
  border        : rgba(255,255,255,0.10)

Scroll reveal JS (déjà livré dans offer_cards.js) :
  IntersectionObserver, opacity 0→1 + translateY(24px→0),
  délai progressif index×80ms, désactivé si prefers-reduced-motion
```

Fichiers de référence déjà livrés à réutiliser/dupliquer pour les nouvelles
cartes services :
- `templates/components/offer_cards.html.twig`
- `assets/styles/components/_offer_cards.scss`
- `assets/js/offer_cards.js`

Pour les cartes services (page `/services` index), dupliquer ce composant en
`service_cards.html.twig` / `_service_cards.scss` en adaptant les classes
`.mg-offer-card` → `.mg-service-card` pour éviter les conflits de style si
les deux composants coexistent sur des pages différentes.

---

## 6. Contraintes techniques à respecter

- **Aucune nouvelle dépendance JS** — vanilla JS uniquement, cohérent avec
  l'existant (`offer_cards.js` comme modèle)
- **Grille Bootstrap conservée** (`col-lg-*`, `col-md-*`) — ne pas redéfinir
  la grille en CSS custom
- **Toutes les chaînes passent par `trans()`** — aucun texte hardcodé dans
  les fichiers Twig, y compris pour les nouvelles pages services
- **SCSS en variables `$mg-*` surchargeables** avec `!default`, cohérent
  avec `_offer_cards.scss`
- **Routes nommées explicitement** (`app_service_sites_web`,
  `app_service_outils_gestion`, etc.) pour faciliter le maillage interne
  via `path()` plutôt que des URLs en dur
- **CTA des pages services** pointent vers `/devis` avec un paramètre
  `?service={slug}` pour pré-remplir le champ "Type de besoin" du formulaire
  (déjà existant : `quote.form.pack` dans le YAML)

---

## 7. Checklist de validation avant mise en ligne

- [ ] Plus aucune clé de traduction non résolue visible (vérifier `services2.cards.ai.*`)
- [ ] Tous les liens sociaux fonctionnels (pas de `URL`/`TITLE` littéral)
- [ ] Les 7 pages services existent, sont en FR et ES, et sont liées entre elles
- [ ] La page `/tarifs` est accessible et liée depuis l'accueil et chaque page service
- [ ] La page À propos affiche les 3 zones (Paris, Espagne, Sénégal) explicitement
- [ ] Le blog est renommé en "Ressources", catégories mises à jour, bio auteur corrigée
- [ ] Chaque page a un title + meta description unique
- [ ] Chaque page a un seul H1
- [ ] Le formulaire de devis pré-remplit correctement selon le pack/service d'origine
- [ ] Test responsive mobile sur les 7 nouvelles pages services + page tarifs
- [ ] Test dark mode sur toutes les nouvelles cartes

---

## 8. Prompt de démarrage suggéré pour Claude Code

```
Lis le fichier BRIEF_REFONTE.md à la racine du projet.

Commence par l'Étape 1 (corrections urgentes) :
1. Trouve et corrige la clé de traduction cassée services2.cards.ai.*
2. Trouve et corrige les liens sociaux cassés sur le template du blog
3. Renomme le titre d'onglet du blog

Une fois ces 3 corrections faites et validées, passe à l'Étape 2
(Accueil + Tarifs) en suivant l'ordre décrit dans la section 3 du brief.

Pour chaque étape, montre-moi les fichiers modifiés avant de passer
à la suivante.
```
