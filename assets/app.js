import 'bootstrap';
import 'jquery';
import 'popper.js';
import 'owl.carousel/dist/assets/owl.carousel.css';
import 'owl.carousel';
import '@fortawesome/fontawesome-free/css/all.min.css';
import '@fortawesome/fontawesome-free/js/all.js';
import 'magnific-popup/dist/magnific-popup.css';
import 'magnific-popup';
import 'jquery-nice-select/css/nice-select.css';
import 'linearicons/dist/web-font/style.css';
import 'jquery-nice-select';
import Choices from 'choices.js';
// Pexels API (client-side usage)
import { createClient } from 'pexels';
import 'bootstrap/dist/css/bootstrap.min.css';
import './vendors/vendors';
import './js/stellar.js';
import './js/theme.js';
import $ from 'jquery';
window.$ = $;
window.jQuery = $;
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

const pexelsClient = createClient('YOUR_PEXELS_API_KEY');

//  Burger Menu
 
 $('.navbar-toggler').on('click', function() {
    $(this).toggleClass('collapsed');
    var expanded = $(this).attr('aria-expanded') === 'true';
    $(this).attr('aria-expanded', !expanded);
    var target = $(this).attr('data-target');
    $(target).toggleClass('show');
});

// Post Commentaires :
document.addEventListener('DOMContentLoaded', () => {
    const commentForm = document.querySelector('#comment-form'); // Assurez-vous que le formulaire a cet ID

    if (commentForm) {
        commentForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // Empêche le rechargement de la page

            const formData = new FormData(commentForm);

            try {
                const response = await fetch(commentForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Indique que c'est une requête AJAX
                    },
                });

                if (!response.ok) {
                    throw new Error('Erreur lors de l\'envoi du formulaire');
                }

                const result = await response.json();

                // Afficher un message de succès
                const messageContainer = document.querySelector('#message-container');
                messageContainer.innerHTML = `<div class="alert alert-success">${result.message}</div>`;

                // Ajouter le nouveau commentaire à la liste
                const commentList = document.querySelector('#comment-list');
                if (commentList) {
                    const newComment = document.createElement('div');
                    newComment.classList.add('comment');
                    newComment.innerHTML = `
                        <p><strong>${result.comment.name}</strong> (${result.comment.createdAt})</p>
                        <p>${result.comment.content}</p>
                    `;
                    commentList.prepend(newComment); // Ajouter au début de la liste
                }

                // Réinitialiser le formulaire
                commentForm.reset();
            } catch (error) {
                const messageContainer = document.querySelector('#message-container');
                messageContainer.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
            }
        });
    }
});
// Post Commentaires :

//CategoriePosts
document.addEventListener('DOMContentLoaded', () => {
    const postsContainer = document.getElementById('posts-container');

    // Délégation d'événement pour les liens de catégories
    document.addEventListener('click', (e) => {
        const link = e.target.closest('.categorie-link');
        if (link) {
            e.preventDefault();
            const categorieId = link.dataset.categorieId;
            if (!categorieId) return;
            document.querySelectorAll('.categorie-link').forEach(el => el.classList.remove('active'));
            link.classList.add('active');
            fetch(`/blog?categorie_id=${categorieId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(response => response.ok ? response.text() : Promise.reject('Erreur réseau'))
                .then(html => {
                    postsContainer.innerHTML = html;
                    window.scrollTo(0, postsContainer.offsetTop); // Scroll to the top of the posts container
                })
                .catch(error => console.error(error));
        }
    });

    // Délégation d'événement pour les liens de pagination
    postsContainer.addEventListener('click', (e) => {
        const link = e.target.closest('.page-link');
        if (link) {
            e.preventDefault();
            const url = link.href;
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(response => response.ok ? response.text() : Promise.reject('Erreur réseau'))
                .then(html => {
                    postsContainer.innerHTML = html;
                    window.scrollTo(0, postsContainer.offsetTop); // Scroll to the top of the posts container
                })
                .catch(error => console.error(error));
        }
    });
});

// Initialisation des plugins
$(document).ready(function () {
    // Owl Carousel
    // ...existing code...
});
//CategoriePosts


// Initialisation des plugins
$(document).ready(function () {
    // Owl Carousel
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        items: 1
    });

    // Magnific Pop-Up
    $('.popup-link').magnificPopup({
        type: 'image',
        gallery: { enabled: true }
    });

        const select = document.querySelector('select');
        const choices = new Choices(select, {
        searchEnabled: false,
        itemSelectText: ''
    });

    // Nice Select
    // $('select').niceSelect();
});

$('#mailchimp-form').submit(function (e) {
    e.preventDefault();
    const email = $('#email').val();

    $.ajax({
        url: 'https://<your-mailchimp-endpoint>',
        type: 'POST',
        data: { email },
        success: function () {
            alert('Thank you for subscribing!');
        },
        error: function () {
            alert('There was an error. Please try again.');
        }
    });
});



console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
