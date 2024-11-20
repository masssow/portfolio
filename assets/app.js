import 'bootstrap';
import 'jquery';
import 'popper.js';
import 'owl.carousel/dist/assets/owl.carousel.css';
import 'owl.carousel';
import '@fortawesome/fontawesome-free/css/all.min.css';

import 'magnific-popup/dist/magnific-popup.css';
import 'magnific-popup';
import 'jquery-nice-select/css/nice-select.css';
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
