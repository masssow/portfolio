/**
 * MassGrafik — Offer Cards interactions
 * Fichier : assets/js/offer_cards.js
 *
 * Fonctionnalités :
 *  - Scroll reveal animé (IntersectionObserver)
 *  - Tracking clics CTA (console + event custom pour GTM/Plausible)
 *  - Aucune dépendance externe
 */

(function () {
    'use strict';

    // -------------------------------------------------------------------------
    // 1. Scroll reveal
    // -------------------------------------------------------------------------

    function initScrollReveal() {
        const cards = document.querySelectorAll('.mg-offer-card');
        if (!cards.length) return;

        // Respect prefers-reduced-motion
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReduced) return;

        // État initial : invisible et légèrement décalé vers le bas
        cards.forEach(function (card, index) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(24px)';
            card.style.transition = 'opacity 0.42s ease, transform 0.42s ease';
            card.style.transitionDelay = (index * 80) + 'ms';
        });

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12 }
        );

        cards.forEach(function (card) {
            observer.observe(card);
        });
    }

    // -------------------------------------------------------------------------
    // 2. Tracking clics CTA
    // -------------------------------------------------------------------------

    function initCtaTracking() {
        var ctaButtons = document.querySelectorAll('.mg-offer-card__cta');
        if (!ctaButtons.length) return;

        ctaButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                // Remonter le nom de l'offre depuis la carte parente
                var card = btn.closest('.mg-offer-card');
                var offerName = card
                    ? (card.querySelector('.mg-offer-card__name') || {}).textContent || 'Inconnu'
                    : 'Inconnu';

                // Log console (pratique en dev)
                console.log('[MassGrafik] CTA cliqué :', offerName.trim());

                // Émettre un événement custom (compatible GTM / Plausible)
                var event = new CustomEvent('mg:offer_cta_click', {
                    bubbles: true,
                    detail: {
                        offer: offerName.trim(),
                        timestamp: Date.now(),
                    },
                });
                document.dispatchEvent(event);

                // --- Plausible (décommenter si utilisé) ---
                // if (typeof plausible === 'function') {
                //     plausible('Offer CTA Click', { props: { offer: offerName.trim() } });
                // }

                // --- GTM dataLayer (décommenter si utilisé) ---
                // window.dataLayer = window.dataLayer || [];
                // window.dataLayer.push({
                //     event: 'offer_cta_click',
                //     offer_name: offerName.trim(),
                // });
            });
        });
    }

    // -------------------------------------------------------------------------
    // 3. Init au chargement du DOM
    // -------------------------------------------------------------------------

    function init() {
        initScrollReveal();
        initCtaTracking();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
