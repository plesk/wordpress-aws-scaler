<?php
if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

class Parallax_One_General_Repeater extends WP_Customize_Control {

    private $options = array();

    /*Class constructor*/
    public function __construct( $manager, $id, $args = array() ) {
        parent::__construct( $manager, $id, $args );
        /*Get options from customizer.php*/
        $this->options = $args;
    }

    public function render_content() {

        /*Counter that helps checking if the box is first and should have the delete button disabled*/
        $it = 0;

        /*Get default options*/
        $this_default = json_decode( $this->setting->default );

        /*Get values (json format)*/
        $values = $this->value();

        /*Decode values*/
        $json = json_decode( $values );

        if( !is_array( $json ) ){
            $json = array($values);
        }

        /*This stores what kind of controls should the repeater have in it*/
        $options = $this->options;

        $allowed_protocols = wp_allowed_protocols();
        array_push($allowed_protocols,'callto');

        if( !empty( $options['parallax_image_control'] ) ){
            $parallax_image_control = $options['parallax_image_control'];
        } else {
            $parallax_image_control = false;
        }

        if( !empty( $options['parallax_icon_control'] ) ){
            $parallax_icon_control = $options['parallax_icon_control'];
            $icons_array = array( 'No Icon','icon-social-blogger','icon-social-blogger-circle','icon-social-blogger-square','icon-social-delicious','icon-social-delicious-circle','icon-social-delicious-square','icon-social-deviantart','icon-social-deviantart-circle','icon-social-deviantart-square','icon-social-dribbble','icon-social-dribbble-circle','icon-social-dribbble-square','icon-social-facebook','icon-social-facebook-circle','icon-social-facebook-square','icon-social-flickr','icon-social-flickr-circle','icon-social-flickr-square','icon-social-googledrive','icon-social-googledrive-alt2','icon-social-googledrive-square','icon-social-googleplus','icon-social-googleplus-circle','icon-social-googleplus-square','icon-google-1','icon-google-2','icon-google-3','icon-google-4','icon-social-instagram','icon-social-instagram-circle','icon-social-instagram-square','icon-social-linkedin','icon-social-linkedin-circle','icon-social-linkedin-square','icon-social-myspace','icon-social-myspace-circle','icon-social-myspace-square','icon-social-picassa','icon-social-picassa-circle','icon-social-picassa-square','icon-social-pinterest','icon-social-pinterest-circle','icon-social-pinterest-square','icon-social-rss','icon-social-rss-circle','icon-social-rss-square','icon-social-skype','icon-social-skype-circle','icon-social-skype-square','icon-social-spotify','icon-social-spotify-circle','icon-social-spotify-square','icon-social-stumbleupon-circle','icon-social-stumbleupon-square','icon-social-tumbleupon','icon-social-tumblr','icon-social-tumblr-circle','icon-social-tumblr-square','icon-social-twitter','icon-social-twitter-circle','icon-social-twitter-square','icon-social-vimeo','icon-social-vimeo-circle','icon-social-vimeo-square','icon-social-wordpress','icon-social-wordpress-circle','icon-social-wordpress-square','icon-social-youtube','icon-social-youtube-circle','icon-social-youtube-square','icon-weather-wind-e','icon-weather-wind-n','icon-weather-wind-ne','icon-weather-wind-nw','icon-weather-wind-s','icon-weather-wind-se','icon-weather-wind-sw','icon-weather-wind-w','icon-software-add-vectorpoint','icon-software-box-oval','icon-software-box-polygon','icon-software-crop','icon-software-eyedropper','icon-software-font-allcaps','icon-software-font-kerning','icon-software-horizontal-align-center','icon-software-layout','icon-software-layout-4boxes','icon-software-layout-header','icon-software-layout-header-2columns','icon-software-layout-header-3columns','icon-software-layout-header-4boxes','icon-software-layout-header-4columns','icon-software-layout-header-complex','icon-software-layout-header-complex2','icon-software-layout-header-complex3','icon-software-layout-header-complex4','icon-software-layout-header-sideleft','icon-software-layout-header-sideright','icon-software-layout-sidebar-left','icon-software-layout-sidebar-right','icon-software-paragraph-align-left','icon-software-paragraph-align-right','icon-software-paragraph-center','icon-software-paragraph-justify-all','icon-software-paragraph-justify-center','icon-software-paragraph-justify-left','icon-software-paragraph-justify-right','icon-software-pathfinder-exclude','icon-software-pathfinder-intersect','icon-software-pathfinder-subtract','icon-software-pathfinder-unite','icon-software-pen','icon-software-pencil','icon-software-scale-expand','icon-software-scale-reduce','icon-software-vector-box','icon-software-vertical-align-bottom','icon-software-vertical-distribute-bottom','icon-music-beginning-button','icon-music-bell','icon-music-eject-button','icon-music-end-button','icon-music-fastforward-button','icon-music-headphones','icon-music-microphone-old','icon-music-mixer','icon-music-pause-button','icon-music-play-button','icon-music-rewind-button','icon-music-shuffle-button','icon-music-stop-button','icon-ecommerce-bag','icon-ecommerce-bag-check','icon-ecommerce-bag-cloud','icon-ecommerce-bag-download','icon-ecommerce-bag-plus','icon-ecommerce-bag-upload','icon-ecommerce-basket-check','icon-ecommerce-basket-cloud','icon-ecommerce-basket-download','icon-ecommerce-basket-upload','icon-ecommerce-bath','icon-ecommerce-cart','icon-ecommerce-cart-check','icon-ecommerce-cart-cloud','icon-ecommerce-cart-content','icon-ecommerce-cart-download','icon-ecommerce-cart-plus','icon-ecommerce-cart-upload','icon-ecommerce-cent','icon-ecommerce-colon','icon-ecommerce-creditcard','icon-ecommerce-diamond','icon-ecommerce-dollar','icon-ecommerce-euro','icon-ecommerce-franc','icon-ecommerce-gift','icon-ecommerce-graph1','icon-ecommerce-graph2','icon-ecommerce-graph3','icon-ecommerce-graph-decrease','icon-ecommerce-graph-increase','icon-ecommerce-guarani','icon-ecommerce-kips','icon-ecommerce-lira','icon-ecommerce-money','icon-ecommerce-naira','icon-ecommerce-pesos','icon-ecommerce-pound','icon-ecommerce-receipt','icon-ecommerce-sale','icon-ecommerce-sales','icon-ecommerce-tugriks','icon-ecommerce-wallet','icon-ecommerce-won','icon-ecommerce-yen','icon-ecommerce-yen2','icon-basic-elaboration-briefcase-check','icon-basic-elaboration-briefcase-download','icon-basic-elaboration-browser-check','icon-basic-elaboration-browser-download','icon-basic-elaboration-browser-plus','icon-basic-elaboration-calendar-check','icon-basic-elaboration-calendar-cloud','icon-basic-elaboration-calendar-download','icon-basic-elaboration-calendar-empty','icon-basic-elaboration-calendar-heart','icon-basic-elaboration-cloud-download','icon-basic-elaboration-cloud-check','icon-basic-elaboration-cloud-search','icon-basic-elaboration-cloud-upload','icon-basic-elaboration-document-check','icon-basic-elaboration-document-graph','icon-basic-elaboration-folder-check','icon-basic-elaboration-folder-cloud','icon-basic-elaboration-mail-document','icon-basic-elaboration-mail-download','icon-basic-elaboration-message-check','icon-basic-elaboration-message-dots','icon-basic-elaboration-message-happy','icon-basic-elaboration-tablet-pencil','icon-basic-elaboration-todolist-2','icon-basic-elaboration-todolist-check','icon-basic-elaboration-todolist-cloud','icon-basic-elaboration-todolist-download','icon-basic-accelerator','icon-basic-anticlockwise','icon-basic-battery-half','icon-basic-bolt','icon-basic-book','icon-basic-book-pencil','icon-basic-bookmark','icon-basic-calendar','icon-basic-cards-hearts','icon-basic-case','icon-basic-clessidre','icon-basic-cloud','icon-basic-clubs','icon-basic-compass','icon-basic-cup','icon-basic-display','icon-basic-download','icon-basic-exclamation','icon-basic-eye','icon-basic-gear','icon-basic-geolocalize-01','icon-basic-geolocalize-05','icon-basic-headset','icon-basic-heart','icon-basic-home','icon-basic-laptop','icon-basic-lightbulb','icon-basic-link','icon-basic-lock','icon-basic-lock-open','icon-basic-magnifier','icon-basic-magnifier-minus','icon-basic-magnifier-plus','icon-basic-mail','icon-basic-mail-multiple','icon-basic-mail-open-text','icon-basic-male','icon-basic-map','icon-basic-message','icon-basic-message-multiple','icon-basic-message-txt','icon-basic-mixer2','icon-basic-notebook-pencil','icon-basic-paperplane','icon-basic-photo','icon-basic-picture','icon-basic-picture-multiple','icon-basic-rss','icon-basic-server2','icon-basic-settings','icon-basic-share','icon-basic-sheet-multiple','icon-basic-sheet-pencil','icon-basic-sheet-txt','icon-basic-tablet','icon-basic-todo','icon-basic-webpage','icon-basic-webpage-img-txt','icon-basic-webpage-multiple','icon-basic-webpage-txt','icon-basic-world','icon-arrows-check','icon-arrows-circle-check','icon-arrows-circle-down','icon-arrows-circle-downleft','icon-arrows-circle-downright','icon-arrows-circle-left','icon-arrows-circle-minus','icon-arrows-circle-plus','icon-arrows-circle-remove','icon-arrows-circle-right','icon-arrows-circle-up','icon-arrows-circle-upleft','icon-arrows-circle-upright','icon-arrows-clockwise','icon-arrows-clockwise-dashed','icon-arrows-down','icon-arrows-down-double-34','icon-arrows-downleft','icon-arrows-downright','icon-arrows-expand','icon-arrows-glide','icon-arrows-glide-horizontal','icon-arrows-glide-vertical','icon-arrows-keyboard-alt','icon-arrows-keyboard-cmd-29','icon-arrows-left','icon-arrows-left-double-32','icon-arrows-move2','icon-arrows-remove','icon-arrows-right','icon-arrows-right-double-31','icon-arrows-rotate','icon-arrows-plus','icon-arrows-shrink','icon-arrows-slim-left','icon-arrows-slim-left-dashed','icon-arrows-slim-right','icon-arrows-slim-right-dashed','icon-arrows-squares','icon-arrows-up','icon-arrows-up-double-33','icon-arrows-upleft','icon-arrows-upright','icon-browser-streamline-window','icon-bubble-comment-streamline-talk','icon-caddie-shopping-streamline','icon-computer-imac','icon-edit-modify-streamline','icon-home-house-streamline','icon-locker-streamline-unlock','icon-lock-locker-streamline','icon-link-streamline','icon-man-people-streamline-user','icon-speech-streamline-talk-user','icon-settings-streamline-2','icon-settings-streamline-1','icon-arrow-carrot-left','icon-arrow-carrot-right','icon-arrow-carrot-up','icon-arrow-carrot-right-alt2','icon-arrow-carrot-down-alt2','icon-arrow-carrot-left-alt2','icon-arrow-carrot-up-alt2','icon-arrow-carrot-2up','icon-arrow-carrot-2right-alt2','icon-arrow-carrot-2up-alt2','icon-arrow-carrot-2right','icon-arrow-carrot-2left-alt2','icon-arrow-carrot-2left','icon-arrow-carrot-2down-alt2','icon-arrow-carrot-2down','icon-arrow-carrot-down','icon-arrow-left','icon-arrow-right','icon-arrow-triangle-down','icon-arrow-triangle-left','icon-arrow-triangle-right','icon-arrow-triangle-up','icon-adjust-vert','icon-bag-alt','icon-box-checked','icon-camera-alt','icon-check','icon-chat-alt','icon-cart-alt','icon-check-alt2','icon-circle-empty','icon-circle-slelected','icon-clock-alt','icon-close-alt2','icon-cloud-download-alt','icon-cloud-upload-alt','icon-compass-alt','icon-creditcard','icon-datareport','icon-easel','icon-lightbulb-alt','icon-laptop','icon-lock-alt','icon-lock-open-alt','icon-link','icon-link-alt','icon-map-alt','icon-mail-alt','icon-piechart','icon-star-half','icon-star-half-alt','icon-star-alt','icon-ribbon-alt','icon-tools','icon-paperclip','icon-adjust-horiz','icon-social-blogger','icon-social-blogger-circle','icon-social-blogger-square','icon-social-delicious','icon-social-delicious-circle','icon-social-delicious-square','icon-social-deviantart','icon-social-deviantart-circle','icon-social-deviantart-square','icon-social-dribbble','icon-social-dribbble-circle','icon-social-dribbble-square','icon-social-facebook','icon-social-facebook-circle','icon-social-facebook-square','icon-social-flickr','icon-social-flickr-circle','icon-social-flickr-square','icon-social-googledrive','icon-social-googledrive-alt2','icon-social-googledrive-square','icon-social-googleplus','icon-social-googleplus-circle','icon-social-googleplus-square','icon-social-instagram','icon-social-instagram-circle','icon-social-instagram-square','icon-social-linkedin','icon-social-linkedin-circle','icon-social-linkedin-square','icon-social-myspace','icon-social-myspace-circle','icon-social-myspace-square','icon-social-picassa','icon-social-picassa-circle','icon-social-picassa-square','icon-social-pinterest','icon-social-pinterest-circle','icon-social-pinterest-square','icon-social-rss','icon-social-rss-circle','icon-social-rss-square','icon-social-share','icon-social-share-circle','icon-social-share-square','icon-social-skype','icon-social-skype-circle','icon-social-skype-square','icon-social-spotify','icon-social-spotify-circle','icon-social-spotify-square','icon-social-stumbleupon-circle','icon-social-stumbleupon-square','icon-social-tumbleupon','icon-social-tumblr','icon-social-tumblr-circle','icon-social-tumblr-square','icon-social-twitter','icon-social-twitter-circle','icon-social-twitter-square','icon-social-vimeo','icon-social-vimeo-circle','icon-social-vimeo-square','icon-social-wordpress','icon-social-wordpress-circle','icon-social-wordpress-square','icon-social-youtube','icon-social-youtube-circle','icon-social-youtube-square','icon-aim','icon-aim-alt','icon-amazon','icon-app-store','icon-apple','icon-behance','icon-creative-commons','icon-dropbox','icon-digg','icon-last','icon-paypal','icon-rss','icon-sharethis','icon-skype','icon-squarespace','icon-technorati','icon-whatsapp','icon-windows','icon-reddit','icon-foursquare','icon-soundcloud','icon-w3','icon-wikipedia','icon-grid-2x2','icon-grid-3x3','icon-menu-square-alt','icon-menu','icon-cloud-alt','icon-tags-alt','icon-tag-alt','icon-gift-alt','icon-comment-alt','icon-icon-phone','icon-icon-mobile','icon-icon-house-alt','icon-icon-house','icon-icon-desktop');
        } else {
             $parallax_icon_control = false;
        }

        if( !empty( $options['parallax_title_control'] ) ){
            $parallax_title_control = $options['parallax_title_control'];
        } else {
            $parallax_title_control = false;
        }

        if( !empty( $options['parallax_subtitle_control'] ) ){
            $parallax_subtitle_control = $options['parallax_subtitle_control'];
        } else {
            $parallax_subtitle_control = false;
        }

        if( !empty( $options['parallax_text_control'] ) ){
            $parallax_text_control = $options['parallax_text_control'];
        } else {
            $parallax_text_control = false;
        }

        if( !empty( $options['parallax_link_control'] ) ){
            $parallax_link_control = $options['parallax_link_control'];
        } else {
            $parallax_link_control = false;
        }

        if( !empty( $options['parallax_shortcode_control'] ) ){
            $parallax_shortcode_control = $options['parallax_shortcode_control'];
        } else {
            $parallax_shortcode_control = false;
        }

        if( !empty( $options['parallax_socials_repeater_control'] ) ){
            $parallax_socials_repeater_control = $options['parallax_socials_repeater_control'];
            $icons_array = array( 'No Icon','icon-social-blogger','icon-social-blogger-circle','icon-social-blogger-square','icon-social-delicious','icon-social-delicious-circle','icon-social-delicious-square','icon-social-deviantart','icon-social-deviantart-circle','icon-social-deviantart-square','icon-social-dribbble','icon-social-dribbble-circle','icon-social-dribbble-square','icon-social-facebook','icon-social-facebook-circle','icon-social-facebook-square','icon-social-flickr','icon-social-flickr-circle','icon-social-flickr-square','icon-social-googledrive','icon-social-googledrive-alt2','icon-social-googledrive-square','icon-social-googleplus','icon-social-googleplus-circle','icon-social-googleplus-square','icon-google-1','icon-google-2','icon-google-3','icon-google-4','icon-social-instagram','icon-social-instagram-circle','icon-social-instagram-square','icon-social-linkedin','icon-social-linkedin-circle','icon-social-linkedin-square','icon-social-myspace','icon-social-myspace-circle','icon-social-myspace-square','icon-social-picassa','icon-social-picassa-circle','icon-social-picassa-square','icon-social-pinterest','icon-social-pinterest-circle','icon-social-pinterest-square','icon-social-rss','icon-social-rss-circle','icon-social-rss-square','icon-social-skype','icon-social-skype-circle','icon-social-skype-square','icon-social-spotify','icon-social-spotify-circle','icon-social-spotify-square','icon-social-stumbleupon-circle','icon-social-stumbleupon-square','icon-social-tumbleupon','icon-social-tumblr','icon-social-tumblr-circle','icon-social-tumblr-square','icon-social-twitter','icon-social-twitter-circle','icon-social-twitter-square','icon-social-vimeo','icon-social-vimeo-circle','icon-social-vimeo-square','icon-social-wordpress','icon-social-wordpress-circle','icon-social-wordpress-square','icon-social-youtube','icon-social-youtube-circle','icon-social-youtube-square','icon-weather-wind-e','icon-weather-wind-n','icon-weather-wind-ne','icon-weather-wind-nw','icon-weather-wind-s','icon-weather-wind-se','icon-weather-wind-sw','icon-weather-wind-w','icon-software-add-vectorpoint','icon-software-box-oval','icon-software-box-polygon','icon-software-crop','icon-software-eyedropper','icon-software-font-allcaps','icon-software-font-kerning','icon-software-horizontal-align-center','icon-software-layout','icon-software-layout-4boxes','icon-software-layout-header','icon-software-layout-header-2columns','icon-software-layout-header-3columns','icon-software-layout-header-4boxes','icon-software-layout-header-4columns','icon-software-layout-header-complex','icon-software-layout-header-complex2','icon-software-layout-header-complex3','icon-software-layout-header-complex4','icon-software-layout-header-sideleft','icon-software-layout-header-sideright','icon-software-layout-sidebar-left','icon-software-layout-sidebar-right','icon-software-paragraph-align-left','icon-software-paragraph-align-right','icon-software-paragraph-center','icon-software-paragraph-justify-all','icon-software-paragraph-justify-center','icon-software-paragraph-justify-left','icon-software-paragraph-justify-right','icon-software-pathfinder-exclude','icon-software-pathfinder-intersect','icon-software-pathfinder-subtract','icon-software-pathfinder-unite','icon-software-pen','icon-software-pencil','icon-software-scale-expand','icon-software-scale-reduce','icon-software-vector-box','icon-software-vertical-align-bottom','icon-software-vertical-distribute-bottom','icon-music-beginning-button','icon-music-bell','icon-music-eject-button','icon-music-end-button','icon-music-fastforward-button','icon-music-headphones','icon-music-microphone-old','icon-music-mixer','icon-music-pause-button','icon-music-play-button','icon-music-rewind-button','icon-music-shuffle-button','icon-music-stop-button','icon-ecommerce-bag','icon-ecommerce-bag-check','icon-ecommerce-bag-cloud','icon-ecommerce-bag-download','icon-ecommerce-bag-plus','icon-ecommerce-bag-upload','icon-ecommerce-basket-check','icon-ecommerce-basket-cloud','icon-ecommerce-basket-download','icon-ecommerce-basket-upload','icon-ecommerce-bath','icon-ecommerce-cart','icon-ecommerce-cart-check','icon-ecommerce-cart-cloud','icon-ecommerce-cart-content','icon-ecommerce-cart-download','icon-ecommerce-cart-plus','icon-ecommerce-cart-upload','icon-ecommerce-cent','icon-ecommerce-colon','icon-ecommerce-creditcard','icon-ecommerce-diamond','icon-ecommerce-dollar','icon-ecommerce-euro','icon-ecommerce-franc','icon-ecommerce-gift','icon-ecommerce-graph1','icon-ecommerce-graph2','icon-ecommerce-graph3','icon-ecommerce-graph-decrease','icon-ecommerce-graph-increase','icon-ecommerce-guarani','icon-ecommerce-kips','icon-ecommerce-lira','icon-ecommerce-money','icon-ecommerce-naira','icon-ecommerce-pesos','icon-ecommerce-pound','icon-ecommerce-receipt','icon-ecommerce-sale','icon-ecommerce-sales','icon-ecommerce-tugriks','icon-ecommerce-wallet','icon-ecommerce-won','icon-ecommerce-yen','icon-ecommerce-yen2','icon-basic-elaboration-briefcase-check','icon-basic-elaboration-briefcase-download','icon-basic-elaboration-browser-check','icon-basic-elaboration-browser-download','icon-basic-elaboration-browser-plus','icon-basic-elaboration-calendar-check','icon-basic-elaboration-calendar-cloud','icon-basic-elaboration-calendar-download','icon-basic-elaboration-calendar-empty','icon-basic-elaboration-calendar-heart','icon-basic-elaboration-cloud-download','icon-basic-elaboration-cloud-check','icon-basic-elaboration-cloud-search','icon-basic-elaboration-cloud-upload','icon-basic-elaboration-document-check','icon-basic-elaboration-document-graph','icon-basic-elaboration-folder-check','icon-basic-elaboration-folder-cloud','icon-basic-elaboration-mail-document','icon-basic-elaboration-mail-download','icon-basic-elaboration-message-check','icon-basic-elaboration-message-dots','icon-basic-elaboration-message-happy','icon-basic-elaboration-tablet-pencil','icon-basic-elaboration-todolist-2','icon-basic-elaboration-todolist-check','icon-basic-elaboration-todolist-cloud','icon-basic-elaboration-todolist-download','icon-basic-accelerator','icon-basic-anticlockwise','icon-basic-battery-half','icon-basic-bolt','icon-basic-book','icon-basic-book-pencil','icon-basic-bookmark','icon-basic-calendar','icon-basic-cards-hearts','icon-basic-case','icon-basic-clessidre','icon-basic-cloud','icon-basic-clubs','icon-basic-compass','icon-basic-cup','icon-basic-display','icon-basic-download','icon-basic-exclamation','icon-basic-eye','icon-basic-gear','icon-basic-geolocalize-01','icon-basic-geolocalize-05','icon-basic-headset','icon-basic-heart','icon-basic-home','icon-basic-laptop','icon-basic-lightbulb','icon-basic-link','icon-basic-lock','icon-basic-lock-open','icon-basic-magnifier','icon-basic-magnifier-minus','icon-basic-magnifier-plus','icon-basic-mail','icon-basic-mail-multiple','icon-basic-mail-open-text','icon-basic-male','icon-basic-map','icon-basic-message','icon-basic-message-multiple','icon-basic-message-txt','icon-basic-mixer2','icon-basic-notebook-pencil','icon-basic-paperplane','icon-basic-photo','icon-basic-picture','icon-basic-picture-multiple','icon-basic-rss','icon-basic-server2','icon-basic-settings','icon-basic-share','icon-basic-sheet-multiple','icon-basic-sheet-pencil','icon-basic-sheet-txt','icon-basic-tablet','icon-basic-todo','icon-basic-webpage','icon-basic-webpage-img-txt','icon-basic-webpage-multiple','icon-basic-webpage-txt','icon-basic-world','icon-arrows-check','icon-arrows-circle-check','icon-arrows-circle-down','icon-arrows-circle-downleft','icon-arrows-circle-downright','icon-arrows-circle-left','icon-arrows-circle-minus','icon-arrows-circle-plus','icon-arrows-circle-remove','icon-arrows-circle-right','icon-arrows-circle-up','icon-arrows-circle-upleft','icon-arrows-circle-upright','icon-arrows-clockwise','icon-arrows-clockwise-dashed','icon-arrows-down','icon-arrows-down-double-34','icon-arrows-downleft','icon-arrows-downright','icon-arrows-expand','icon-arrows-glide','icon-arrows-glide-horizontal','icon-arrows-glide-vertical','icon-arrows-keyboard-alt','icon-arrows-keyboard-cmd-29','icon-arrows-left','icon-arrows-left-double-32','icon-arrows-move2','icon-arrows-remove','icon-arrows-right','icon-arrows-right-double-31','icon-arrows-rotate','icon-arrows-plus','icon-arrows-shrink','icon-arrows-slim-left','icon-arrows-slim-left-dashed','icon-arrows-slim-right','icon-arrows-slim-right-dashed','icon-arrows-squares','icon-arrows-up','icon-arrows-up-double-33','icon-arrows-upleft','icon-arrows-upright','icon-browser-streamline-window','icon-bubble-comment-streamline-talk','icon-caddie-shopping-streamline','icon-computer-imac','icon-edit-modify-streamline','icon-home-house-streamline','icon-locker-streamline-unlock','icon-lock-locker-streamline','icon-link-streamline','icon-man-people-streamline-user','icon-speech-streamline-talk-user','icon-settings-streamline-2','icon-settings-streamline-1','icon-arrow-carrot-left','icon-arrow-carrot-right','icon-arrow-carrot-up','icon-arrow-carrot-right-alt2','icon-arrow-carrot-down-alt2','icon-arrow-carrot-left-alt2','icon-arrow-carrot-up-alt2','icon-arrow-carrot-2up','icon-arrow-carrot-2right-alt2','icon-arrow-carrot-2up-alt2','icon-arrow-carrot-2right','icon-arrow-carrot-2left-alt2','icon-arrow-carrot-2left','icon-arrow-carrot-2down-alt2','icon-arrow-carrot-2down','icon-arrow-carrot-down','icon-arrow-left','icon-arrow-right','icon-arrow-triangle-down','icon-arrow-triangle-left','icon-arrow-triangle-right','icon-arrow-triangle-up','icon-adjust-vert','icon-bag-alt','icon-box-checked','icon-camera-alt','icon-check','icon-chat-alt','icon-cart-alt','icon-check-alt2','icon-circle-empty','icon-circle-slelected','icon-clock-alt','icon-close-alt2','icon-cloud-download-alt','icon-cloud-upload-alt','icon-compass-alt','icon-creditcard','icon-datareport','icon-easel','icon-lightbulb-alt','icon-laptop','icon-lock-alt','icon-lock-open-alt','icon-link','icon-link-alt','icon-map-alt','icon-mail-alt','icon-piechart','icon-star-half','icon-star-half-alt','icon-star-alt','icon-ribbon-alt','icon-tools','icon-paperclip','icon-adjust-horiz','icon-social-blogger','icon-social-blogger-circle','icon-social-blogger-square','icon-social-delicious','icon-social-delicious-circle','icon-social-delicious-square','icon-social-deviantart','icon-social-deviantart-circle','icon-social-deviantart-square','icon-social-dribbble','icon-social-dribbble-circle','icon-social-dribbble-square','icon-social-facebook','icon-social-facebook-circle','icon-social-facebook-square','icon-social-flickr','icon-social-flickr-circle','icon-social-flickr-square','icon-social-googledrive','icon-social-googledrive-alt2','icon-social-googledrive-square','icon-social-googleplus','icon-social-googleplus-circle','icon-social-googleplus-square','icon-social-instagram','icon-social-instagram-circle','icon-social-instagram-square','icon-social-linkedin','icon-social-linkedin-circle','icon-social-linkedin-square','icon-social-myspace','icon-social-myspace-circle','icon-social-myspace-square','icon-social-picassa','icon-social-picassa-circle','icon-social-picassa-square','icon-social-pinterest','icon-social-pinterest-circle','icon-social-pinterest-square','icon-social-rss','icon-social-rss-circle','icon-social-rss-square','icon-social-share','icon-social-share-circle','icon-social-share-square','icon-social-skype','icon-social-skype-circle','icon-social-skype-square','icon-social-spotify','icon-social-spotify-circle','icon-social-spotify-square','icon-social-stumbleupon-circle','icon-social-stumbleupon-square','icon-social-tumbleupon','icon-social-tumblr','icon-social-tumblr-circle','icon-social-tumblr-square','icon-social-twitter','icon-social-twitter-circle','icon-social-twitter-square','icon-social-vimeo','icon-social-vimeo-circle','icon-social-vimeo-square','icon-social-wordpress','icon-social-wordpress-circle','icon-social-wordpress-square','icon-social-youtube','icon-social-youtube-circle','icon-social-youtube-square','icon-aim','icon-aim-alt','icon-amazon','icon-app-store','icon-apple','icon-behance','icon-creative-commons','icon-dropbox','icon-digg','icon-last','icon-paypal','icon-rss','icon-sharethis','icon-skype','icon-squarespace','icon-technorati','icon-whatsapp','icon-windows','icon-reddit','icon-foursquare','icon-soundcloud','icon-w3','icon-wikipedia','icon-grid-2x2','icon-grid-3x3','icon-menu-square-alt','icon-menu','icon-cloud-alt','icon-tags-alt','icon-tag-alt','icon-gift-alt','icon-comment-alt','icon-icon-phone','icon-icon-mobile','icon-icon-house-alt','icon-icon-house','icon-icon-desktop');
        } else {
            $parallax_socials_repeater_control = false;
        }
?>
        <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        <div class="parallax_one_general_control_repeater parallax_one_general_control_droppable">
        <?php
        /* If there are no values*/
        if( empty( $json ) ) { ?>
            <div class="parallax_one_general_control_repeater_container">
                <div class="parallax-customize-control-title"><?php esc_html_e('Parallax One','parallax-one')?></div>
                <div class="parallax-box-content-hidden">
                <?php
                /*If both image and icon control are enabled you should be able to choose if you want to upload an image or to choose an icon*/
                if($parallax_image_control == true && $parallax_icon_control == true){ ?>
                    <span class="customize-control-title"><?php esc_html_e('Image type','parallax-one');?></span>
                    <select class="parallax_one_image_choice">
                        <option value="parallax_icon" selected><?php esc_html_e('Icon','parallax-one'); ?></option>
                        <option value="parallax_image"><?php esc_html_e('Image','parallax-one'); ?></option>
                        <option value="parallax_none"><?php esc_html_e('None','parallax-one'); ?></option>
                    </select>
                    <p class="parallax_one_image_control" style="display:none">
                        <span class="customize-control-title"><?php esc_html_e('Image','parallax-one')?></span>
                        <input type="text" class="widefat custom_media_url">
                        <input type="button" class="button button-primary custom_media_button_parallax_one" value="<?php esc_html_e('Upload Image','parallax-one'); ?>" />
                    </p>
                    <div class="parallax_one_general_control_icon">
                        <span class="customize-control-title"><?php esc_html_e('Icon','parallax-one')?></span>
                        <div class="islemag-dd">
                            <select name="<?php echo esc_attr($this->id); ?>" class="parallax_one_icon_control">
                            <?php
                            foreach($icons_array as $key => $value) {
                                echo '<option value="'.esc_attr($value).'" data-iconclass="'.esc_attr($value).'" >'.esc_attr($value).'</option>';
                            } ?>
                            </select>
                        </div>
                    </div>
                <?php
                } else {
                    if($parallax_image_control ==true){	?>
                        <span class="customize-control-title"><?php esc_html_e('Image','parallax-one')?></span>
                        <p class="parallax_one_image_control">
                            <input type="text" class="widefat custom_media_url">
                            <input type="button" class="button button-primary custom_media_button_parallax_one" value="<?php esc_html_e('Upload Image','parallax-one'); ?>" />
                        </p>
                    <?php
                    }

                    if($parallax_icon_control ==true){ ?>
                        <span class="customize-control-title"><?php esc_html_e('Icon','parallax-one')?></span>
                        <div class="parallax-dd">
                            <select name="<?php echo esc_attr($this->id); ?>" class="parallax_one_icon_control">
                            <?php
                            foreach($icons_array as $key => $value) {
                                echo '<option value="'.esc_attr($value).'" '.selected($icon->icon_value,$value).' data-iconclass="'.esc_attr($value).'" >'.esc_attr($value).'</option>';
                            }
                            ?>
                            </select>
                        </div>
                    <?php 
                    }
                }

                                    
                if($parallax_title_control==true){ ?>
                    <span class="customize-control-title"><?php esc_html_e('Title','parallax-one')?></span>
                    <input type="text" class="parallax_one_title_control" placeholder="<?php esc_html_e('Title','parallax-one'); ?>"/>
                <?php
                }

                if($parallax_subtitle_control==true){ ?>
                    <span class="customize-control-title"><?php esc_html_e('Subtitle','parallax-one')?></span>
                    <input type="text" class="parallax_one_subtitle_control" placeholder="<?php esc_html_e('Subtitle','parallax-one'); ?>"/>
                <?php
                }

                if($parallax_text_control==true){?>
                    <span class="customize-control-title"><?php esc_html_e('Text','parallax-one')?></span>
                    <span class="description customize-control-description"><?php esc_html_e('Allowed html: br, em, strong, a, button, ul, li','parallax-one');?></span>
                    <textarea class="parallax_one_text_control" placeholder="<?php esc_html_e('Text','parallax-one'); ?>"></textarea>
                <?php 
                }

                if($parallax_link_control==true){ ?>
                    <span class="customize-control-title"><?php esc_html_e('Link','parallax-one')?></span>
                    <input type="text" class="parallax_one_link_control" placeholder="<?php esc_html_e('Link','parallax-one'); ?>"/>
                <?php 
                }
                
                if($parallax_shortcode_control==true){ ?>
                    <span class="customize-control-title"><?php esc_html_e('Shortcode','parallax-one')?></span>
                    <input type="text" class="parallax_one_shortcode_control" placeholder="<?php esc_html_e('Shortcode','parallax-one'); ?>"/>
                <?php
                }

                if($parallax_socials_repeater_control==true){ ?>
                    <span class="customize-control-title"><?php esc_html_e('Social icons','parallax-one')?></span>
                    <div class="parallax-one-social-repeater">
                        <div class="parallax-one-social-repeater-container">
                            <div class="parallax-dd">
                                <select name="<?php echo esc_attr($this->id); ?>" class="parallax_one_icon_control">
                                <?php
                                foreach($icons_array as $value) {
                                    echo '<option value="'.esc_attr($value).'" '.selected($value, 'No Icon').' data-iconclass="'.esc_attr($value).'" >'.esc_attr($value).'</option>';
                                }
                                ?>
                                </select>
                            </div>
                            <input type="text" class="parallax_one_social_repeater_link" placeholder="<?php esc_html_e( 'Link', 'parallax-one' ); ?>">
                            <input type="hidden" class="parallax_one_social_repeater_id" value="">
                            <button class="parallax_one_remove_social_item" style="display:none"><?php esc_html_e('X','parallax-one'); ?></button>
                        </div>
                        <input type="hidden" id="parallax_one_socials_repeater_colector" <?php $this->link(); ?> class="parallax_one_socials_repeater_colector" value="" />
                    </div>
                    <button class="parallax_one_add_social_item"><?php esc_html_e('Add icon','parallax-one'); ?></button>
                <?php
                } ?>
                
                <input type="hidden" class="parallax_one_box_id">
                <button type="button" class="parallax_one_general_control_remove_field button" style="display:none;"><?php esc_html_e('Delete field','parallax-one'); ?></button>
                </div> <!-- end .parallax-box-content-hidden -->
            </div> <!-- end .parallax_one_general_control_repeater_container -->
        <?php
        } else {
            /*There are no values set but there are default values*/
            if ( !empty( $this_default ) && empty( $json ) ) {
                foreach($this_default as $icon){ ?>
                    <div class="parallax_one_general_control_repeater_container parallax_one_draggable">
                        <div class="parallax-customize-control-title"><?php esc_html_e('Parallax One','parallax-one')?></div>
                        <div class="parallax-box-content-hidden">
                        <?php
                        /*If both image and icon control are enabled you should be able to choose if you want to upload an image or to choose an icon*/
                        if($parallax_image_control == true && $parallax_icon_control == true){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Image type','parallax-one');?></span>
                            <select class="parallax_one_image_choice">
                                <option value="parallax_icon" <?php selected( $icon->choice, 'parallax_icon' );?>><?php esc_html_e( 'Icon', 'parallax-one' ); ?></option>
                                <option value="parallax_image" <?php selected( $icon->choice, 'parallax_image' );?>><?php esc_html_e( 'Image', 'parallax-one' ); ?></option>
                                <option value="parallax_none" <?php selected( $icon->choice, 'parallax_none' );?>><?php esc_html_e( 'None', 'parallax-one' ); ?></option>
                            </select>
                            <p class="parallax_one_image_control"  <?php if( !empty( $icon->choice ) && $icon->choice != 'parallax_image' ){ echo 'style="display:none"';} ?>>
                                <span class="customize-control-title"><?php esc_html_e( 'Image', 'parallax-one' ); ?></span>
                                <input type="text" class="widefat custom_media_url" value="<?php if(!empty($icon->image_url)) {echo esc_attr($icon->image_url);} ?>">
                                <input type="button" class="button button-primary custom_media_button_parallax_one" value="<?php esc_html_e('Upload Image','parallax-one'); ?>" />
                            </p>
                            <div class="parallax_one_general_control_icon" <?php if( !empty( $icon->choice ) && $icon->choice != 'parallax_icon'){ echo 'style="display:none"';} ?>>
                                <span class="customize-control-title"><?php esc_html_e('Icon','parallax-one')?></span>
                                <div class="parallax-dd">
                                    <select name="<?php echo esc_attr($this->id); ?>" class="parallax_one_icon_control">
                                    <?php
                                    foreach($icons_array as $key => $value) {
                                        echo '<option value="'.esc_attr( $value ).'" '.selected( $icon->icon_value, $value ).' data-iconclass="'.esc_attr( $value ).'" >'.esc_attr( $value ).'</option>';
                                    }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        <?php
                        } else { 

                            if( $parallax_image_control == true ){ ?>
                                <span class="customize-control-title"><?php esc_html_e('Image','parallax-one')?></span>
                                <p class="parallax_one_image_control">
                                    <input type="text" class="widefat custom_media_url" value="<?php if(!empty($icon->image_url)) {echo esc_attr($icon->image_url);} ?>">
                                    <input type="button" class="button button-primary custom_media_button_parallax_one" value="<?php esc_html_e('Upload Image','parallax-one'); ?>" />
                                </p>
                            <?php	
                            }

                            if( $parallax_icon_control == true ){ ?>
                                <span class="customize-control-title"><?php esc_html_e('Icon','parallax-one')?></span>
                                <div class="parallax-dd">
                                    <select name="<?php echo esc_attr( $this->id ); ?>" class="parallax_one_icon_control">
                                    <?php
                                    foreach($icons_array as $key => $value) {
                                        echo '<option value="'.esc_attr($value).'" '.selected($icon->icon_value,$value).' data-iconclass="'.esc_attr($value).'" >'.esc_attr($value).'</option>';
                                    }
                                    ?>
                                    </select>
                                </div>
                            <?php
                            }
                        }

                        if( $parallax_title_control == true ){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Title','parallax-one')?></span>
                            <input type="text" value="<?php if(!empty($icon->title)) echo esc_attr($icon->title); ?>" class="parallax_one_title_control" placeholder="<?php esc_html_e('Title','parallax-one'); ?>"/>
                        <?php
                        }

                        if( $parallax_subtitle_control == true ){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Subtitle','parallax-one')?></span>
                            <input type="text" value="<?php if(!empty($icon->subtitle)) echo esc_attr($icon->subtitle); ?>" class="parallax_one_subtitle_control" placeholder="<?php esc_html_e('Subtitle','parallax-one'); ?>"/>
                        <?php
                        }

                        if( $parallax_text_control == true ){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Text','parallax-one')?></span>
                            <span class="description customize-control-description"><?php esc_html_e('Allowed html: br, em, strong, a, button, ul, li','parallax-one');?></span>
                            <textarea placeholder="<?php esc_html_e('Text','parallax-one'); ?>" class="parallax_one_text_control"><?php if(!empty($icon->text)) {echo esc_attr($icon->text);} ?></textarea>
                        <?php
                        }
                        
                        if($parallax_link_control){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Link','parallax-one')?></span>
                            <input type="text" value="<?php if(!empty($icon->link)) echo esc_url($icon->link, $allowed_protocols); ?>" class="parallax_one_link_control" placeholder="<?php esc_html_e('Link','parallax-one'); ?>"/>
                        <?php

                        }
                        
                        if($parallax_shortcode_control==true){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Shortcode','parallax-one')?></span>
                            <input type="text" value='<?php if(!empty($icon->shortcode)) echo $icon->shortcode; ?>' class="parallax_one_shortcode_control" placeholder="<?php esc_html_e('Shortcode','parallax-one'); ?>"/>
                        <?php 
                        }
                        
                        if($parallax_socials_repeater_control==true){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Social icons','parallax-one')?></span>
                            <div class="parallax-one-social-repeater">
                            <?php
                            if( isset( $icon->social_repeater ) && !empty( $icon->social_repeater ) ){
                                /*This counter check to see if is first icon in repeater and hide the delete button if it is*/
                                $show_del = 0;
                                $social_repeater = json_decode(html_entity_decode($icon->social_repeater),true);
                                foreach( $social_repeater as $social_icon ){ 
                                    $show_del++; ?>
                                    <div class="parallax-one-social-repeater-container">
                                        <div class="parallax-dd">
                                            <select name="<?php echo esc_attr($this->id); ?>" class="parallax_one_icon_control">
                                            <?php
                                            foreach($icons_array as $value) {
                                                echo '<option value="'.esc_attr( $value ).'" '.selected( $social_icon['icon'], $value ).' data-iconclass="'.esc_attr( $value ).'" >'.esc_attr( $value ).'</option>';
                                            } ?>
                                            </select>
                                        </div>
                                        <input type="text" class="parallax_one_social_repeater_link" placeholder="<?php esc_html_e( 'Link', 'parallax-one' ); ?>" value="<?php if( !empty($social_icon['link']) ) echo esc_url($social_icon['link']); ?>" >
                                        
                                        <input type="hidden" class="parallax_one_social_repeater_id" value="<?php if(!empty($social_icon['id'])) echo esc_attr($social_icon['id']); ?>">
                                        <button type="button" class="button parallax_one_remove_social_item" style="display:none"><?php esc_html_e('X','parallax-one'); ?></button>
                                    </div>
                                <?php
                                } ?>
                                <input type="hidden" id="parallax_one_socials_repeater_colector" <?php $this->link(); ?> class="parallax_one_socials_repeater_colector" value="<?php echo esc_textarea( html_entity_decode( $icon->social_repeater ) ); ?>" />
                                </div>
                                <button class="parallax_one_add_social_item"><?php esc_html_e('Add icon','parallax-one'); ?></button>
                            <?php
                            } else { ?>
                                <div class="parallax-one-social-repeater-container">
                                    <div class="parallax-dd">
                                        <select name="<?php echo esc_attr($this->id); ?>" class="parallax_one_icon_control">
                                        <?php
                                            foreach($icons_array as $value) {
                                                echo '<option value="'.esc_attr($value).'" '.selected($value,"No Icon").' data-iconclass="'.esc_attr($value).'" >'.esc_attr($value).'</option>';
                                            } ?>
                                        </select>
                                    </div>
                                    <input type="text" class="parallax_one_social_repeater_link" placeholder="<?php esc_html_e( 'Link', 'parallax-one' ); ?>" value="" >
                                    <input type="hidden" class="parallax_one_social_repeater_id" value="">
                                    <button class="parallax_one_remove_social_item" style="display:none"><?php esc_html_e('X','parallax-one'); ?></button>
                                </div>
                                <input type="hidden" id="parallax_one_socials_repeater_colector" class="parallax_one_socials_repeater_colector" value="" />
                                </div>
                                <button class="parallax_one_add_social_item"><?php esc_html_e('Add icon','parallax-one'); ?></button>
                            <?php
                            }
                        } ?>
                                        
                        <input type="hidden" class="parallax_one_box_id" value="<?php if(!empty($icon->id)) echo esc_attr($icon->id); ?>">
                        <button type="button" class="parallax_one_general_control_remove_field button" <?php if ($it == 0) echo 'style="display:none;"'; ?>><?php esc_html_e('Delete field','parallax-one'); ?></button>
                        </div> <!-- end .parallax-box-content-hidden -->
                    </div> <!-- end .parallax_one_general_control_repeater_container -->
                <?php
                    $it++;
                }
            } else {
                            
                foreach($json as $icon){ ?>
                    <div class="parallax_one_general_control_repeater_container parallax_one_draggable">
                        <div class="parallax-customize-control-title"><?php esc_html_e('Parallax One','parallax-one')?></div>
                        <div class="parallax-box-content-hidden">
                        <?php
                        if($parallax_image_control == true && $parallax_icon_control == true){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Image type','parallax-one');?></span>
                            <select class="parallax_one_image_choice">
                                <option value="parallax_icon" <?php selected( $icon->choice, 'parallax_icon' );?>><?php esc_html_e( 'Icon', 'parallax-one' );?></option>
                                <option value="parallax_image" <?php selected( $icon->choice, 'parallax_image' );?>><?php esc_html_e( 'Image', 'parallax-one' );?></option>
                                <option value="parallax_none" <?php selected( $icon->choice, 'parallax_none' );?>><?php esc_html_e( 'None', 'parallax-one' );?></option>
                            </select>
                            <p class="parallax_one_image_control" <?php if(!empty($icon->choice) && $icon->choice!='parallax_image'){ echo 'style="display:none"';}?>>
                                <span class="customize-control-title"><?php esc_html_e('Image','parallax-one');?></span>
                                <input type="text" class="widefat custom_media_url" value="<?php if(!empty($icon->image_url)) {echo esc_attr($icon->image_url);} ?>">
                                <input type="button" class="button button-primary custom_media_button_parallax_one" value="<?php esc_html_e('Upload Image','parallax-one'); ?>" />
                            </p>
                            <div class="parallax_one_general_control_icon" <?php  if(!empty($icon->choice) && $icon->choice!='parallax_icon'){ echo 'style="display:none"';}?>>
                                <span class="customize-control-title"><?php esc_html_e('Icon','parallax-one')?></span>
                                <div class="parallax-dd">
                                    <select name="<?php echo esc_attr($this->id); ?>" class="parallax_one_icon_control">
                                    <?php
                                    foreach($icons_array as $key => $value) {
                                        echo '<option value="'.esc_attr($value).'" '.selected($icon->icon_value,$value).' data-iconclass="'.esc_attr($value).'" >'.esc_attr($value).'</option>';
                                    } ?>
                                    </select>
                                </div>
                            </div>
                        <?php
                        } else {

                            if($parallax_image_control == true){ ?>
                                <span class="customize-control-title"><?php esc_html_e('Image','parallax-one')?></span>
                                <p class="parallax_one_image_control">
                                    <input type="text" class="widefat custom_media_url" value="<?php if(!empty($icon->image_url)) {echo esc_attr($icon->image_url);} ?>">
                                    <input type="button" class="button button-primary custom_media_button_parallax_one" value="<?php esc_html_e('Upload Image','parallax-one'); ?>" />
                                </p>
                            <?php 
                            }

                            if($parallax_icon_control==true){ ?>
                                <span class="customize-control-title"><?php esc_html_e('Icon','parallax-one')?></span>
                                <div class="parallax-dd">
                                    <select name="<?php echo esc_attr($this->id); ?>" class="parallax_one_icon_control">
                                    <?php
                                    foreach($icons_array as $key => $value) {
                                        echo '<option value="'.esc_attr($value).'" '.selected($icon->icon_value,$value).' data-iconclass="'.esc_attr($value).'" >'.esc_attr($value).'</option>';
                                    } ?>
                                    </select>
                                </div>
                            <?php
                            }
                        }
                                        
                        if($parallax_title_control==true){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Title','parallax-one')?></span>
                            <input type="text" value="<?php if(!empty($icon->title)) echo esc_attr($icon->title); ?>" class="parallax_one_title_control" placeholder="<?php esc_html_e('Title','parallax-one'); ?>"/>
                        <?php
                        }

                        if($parallax_subtitle_control==true){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Subtitle','parallax-one')?></span>
                            <input type="text" value="<?php if(!empty($icon->subtitle)) echo esc_attr($icon->subtitle); ?>" class="parallax_one_subtitle_control" placeholder="<?php esc_html_e('Subtitle','parallax-one'); ?>"/>
                        <?php
                        }
                                        
                        if($parallax_text_control==true ){?>
                            <span class="customize-control-title"><?php esc_html_e('Text','parallax-one')?></span>
                            <span class="description customize-control-description"><?php esc_html_e('Allowed html: br, em, strong, a, button, ul, li','parallax-one');?></span>
                            <textarea class="parallax_one_text_control" placeholder="<?php esc_html_e('Text','parallax-one'); ?>"><?php if(!empty($icon->text)) {echo esc_attr($icon->text);} ?></textarea>
                        <?php 
                        }

                        if($parallax_link_control){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Link','parallax-one')?></span>
                            <input type="text" value="<?php if(!empty($icon->link)) echo esc_url($icon->link, $allowed_protocols); ?>" class="parallax_one_link_control" placeholder="<?php esc_html_e('Link','parallax-one'); ?>"/>
                        <?php
                        }

                        if($parallax_shortcode_control==true){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Shortcode','parallax-one')?></span>
                            <input type="text" value='<?php if(!empty($icon->shortcode)) echo $icon->shortcode; ?>' class="parallax_one_shortcode_control" placeholder="<?php esc_html_e('Shortcode','parallax-one'); ?>"/>
                        <?php 
                        }

                        if( $parallax_socials_repeater_control==true ){ ?>
                            <span class="customize-control-title"><?php esc_html_e('Social icons','parallax-one'); ?></span>
                            <div class="parallax-one-social-repeater">
                            <?php
                            if(isset($icon->social_repeater) && !empty($icon->social_repeater)){
                                $show_del = 0;
                                $social_repeater = json_decode(html_entity_decode($icon->social_repeater), true);

                                foreach( $social_repeater as $social_icon ){
                                    $show_del++; ?>
                                    <div class="parallax-one-social-repeater-container">
                                        <div class="parallax-dd">
                                            <select name="<?php echo esc_attr($this->id); ?>" class="parallax_one_icon_control">
                                            <?php
                                            foreach($icons_array as $value) {
                                                echo '<option value="'.esc_attr($value).'" '.selected($social_icon['icon'], $value).' data-iconclass="'.esc_attr($value).'" >'.esc_attr($value).'</option>';
                                            } ?>
                                            </select>
                                        </div>
                                        <input type="text" class="parallax_one_social_repeater_link" placeholder="<?php esc_html_e( 'Link', 'parallax-one' ); ?>" value="<?php if( !empty($social_icon['link']) ) echo esc_url($social_icon['link']); ?>" >
                                        <input type="hidden" class="parallax_one_social_repeater_id" value="<?php if(!empty($social_icon['id'])) echo esc_attr($social_icon['id']); ?>">
                                        <button class="parallax_one_remove_social_item" style="<?php if($show_del == 1) echo "display:none"; ?>"><?php esc_html_e('X','parallax-one'); ?></button>
                                    </div>
                                <?php
                                } ?>
                                <input type="hidden" id="parallax_one_socials_repeater_colector" class="parallax_one_socials_repeater_colector" value="<?php echo esc_textarea( html_entity_decode( $icon->social_repeater ) ); ?>" />
                                </div>
                                <button class="parallax_one_add_social_item"><?php esc_html_e('Add icon','parallax-one'); ?></button>
                            <?php
                            } else { ?>
                                <div class="parallax-one-social-repeater-container">
                                  <div class="parallax-dd">
                                        <select name="<?php echo esc_attr($this->id); ?>" class="parallax_one_icon_control">
                                        <?php
                                        foreach($icons_array as $value) {
                                            echo '<option value="'.esc_attr($value).'" '.selected($value,"No Icon").' data-iconclass="'.esc_attr($value).'" >'.esc_attr($value).'</option>';
                                        } ?>
                                        </select>
                                    </div>
                                    <input type="text" class="parallax_one_social_repeater_link" placeholder="<?php esc_html_e( 'Link', 'parallax-one' ); ?>" value="" >
                                    <input type="hidden" class="parallax_one_social_repeater_id" value="">
                                    <button class="parallax_one_remove_social_item" style="display:none"><?php esc_html_e('X','parallax-one'); ?></button>
                                </div>
                                <input type="hidden" id="parallax_one_socials_repeater_colector" class="parallax_one_socials_repeater_colector" value="" />
                                </div>
                                <button class="parallax_one_add_social_item"><?php esc_html_e('Add icon','parallax-one'); ?></button>
                            <?php
                            }  
                        } ?>
                                        
                        <input type="hidden" class="parallax_one_box_id" value="<?php if(!empty($icon->id)) echo esc_attr($icon->id); ?>">
                        <button type="button" class="parallax_one_general_control_remove_field button" <?php if ($it == 0) echo 'style="display:none;"'; ?>><?php esc_html_e('Delete field','parallax-one'); ?></button>
                        </div><!-- end .parallax-box-content-hidden -->
                    </div><!-- end .parallax_one_general_control_repeater_container -->
                    <?php
                    $it++;
                }
            }
        }

        if ( !empty( $this_default ) && empty( $json ) ) { ?>
            <input type="hidden" id="parallax_one_<?php echo $options['section']; ?>_repeater_colector" <?php $this->link(); ?> class="parallax_one_repeater_colector" value="<?php  echo esc_textarea( json_encode($this_default )); ?>" />
        <?php 
        } else { ?>
            <input type="hidden" id="parallax_one_<?php echo $options['section']; ?>_repeater_colector" <?php $this->link(); ?> class="parallax_one_repeater_colector" value="<?php echo esc_textarea( $this->value() ); ?>" />
        <?php 
        } ?>
        </div>
        <button type="button"   class="button add_field parallax_one_general_control_new_field"><?php esc_html_e('Add new field','parallax-one'); ?></button>
    <?php
    }
}