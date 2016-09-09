jQuery(document).ready(function() {
    var parallax_one_aboutpage = parallaxOneWelcomeScreenCustomizerObject.aboutpage;

    /* Upsell in Customizer (Link to Welcome page) */
    if ( !jQuery( ".parallax-upsells" ).length ) {
        jQuery('#customize-theme-controls > ul').prepend('<li class="accordion-section parallax-upsells">');
    }

    if (typeof parallax_one_aboutpage !== 'undefined') {		 +
        jQuery('.parallax-upsells').append('<a style="width: 80%; margin: 5px auto 5px auto; display: block; text-align: center;" href="' + parallax_one_aboutpage + '" class="button" target="_blank">{themeinfo}</a>'.replace('{themeinfo}', parallaxOneWelcomeScreenCustomizerObject.themeinfo));
    }

    if ( !jQuery( ".parallax-upsells" ).length ) {
        jQuery('#customize-theme-controls > ul').prepend('</li>');
    }
});