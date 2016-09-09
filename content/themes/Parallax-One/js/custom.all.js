jQuery(window).load(function(){ 
    callback_menu_align();
    fixFooterBottom();
});

jQuery(window).resize(function(){
    callback_menu_align();
    fixFooterBottom();
});


/* CENTERED MENU */
var callback_menu_align = function () {
    var headerWrap      = jQuery('header.header');
    var navWrap         = jQuery('.main-navigation');
    var logoWrap        = jQuery('.navbar-header');
    var containerWrap   = jQuery('.container');
    var classToAdd      = 'menu-align-center';
    if ( headerWrap.hasClass(classToAdd) ) {
        headerWrap.removeClass(classToAdd);
    }
    var logoWidth       = logoWrap.outerWidth();
    var menuWidth       = navWrap.outerWidth();
    var containerWidth  = containerWrap.width();
    if ( menuWidth + logoWidth > containerWidth ) {
        headerWrap.addClass(classToAdd);
    } else {
        if ( headerWrap.hasClass(classToAdd) ) {
            headerWrap.removeClass(classToAdd);
        }
    }
    jQuery('.sticky-navigation-open').css('min-height','70');
    var headerHeight = jQuery('.sticky-navigation').outerHeight();
    if ( headerHeight > 70 ) {
        jQuery('.sticky-navigation-open').css('min-height', headerHeight);
    } else {
        jQuery('.sticky-navigation-open').css('min-height', 70);
    }
}

/* STICKY FOOTER */
function fixFooterBottom(){
    var header      = jQuery('header.header');
    var footer      = jQuery('footer.footer');
    var content     = jQuery('.content-wrap');
    content.css('min-height', '1px');
    var headerHeight  = header.outerHeight();
    var footerHeight  = footer.outerHeight();
    var contentHeight = content.outerHeight();
    var windowHeight  = jQuery(window).height();
    var totalHeight = headerHeight + footerHeight + contentHeight;
    if (totalHeight<windowHeight){
      content.css('min-height', windowHeight - headerHeight - footerHeight );
    } else {
      content.css('min-height','1px');
    }
}

jQuery(document).ready(function($) {
    "use strict";
    /*---------------------------------------*/
    /*	BOOTSTRAP FIXES
	/*---------------------------------------*/
    var oldSSB = jQuery.fn.modal.Constructor.prototype.setScrollbar;
    $.fn.modal.Constructor.prototype.setScrollbar = function() {
        oldSSB.apply(this);
        if (this.scrollbarWidth) jQuery('.navbar-fixed-top').css('padding-right', this.scrollbarWidth);
    }
    var oldRSB = $.fn.modal.Constructor.prototype.resetScrollbar;
    $.fn.modal.Constructor.prototype.resetScrollbar = function() {
        oldRSB.apply(this);
        jQuery('.navbar-fixed-top').css('padding-right', '');
    }
    if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
        var msViewportStyle = document.createElement('style')
        msViewportStyle.appendChild(
            document.createTextNode(
                '@-ms-viewport{width:auto!important}'
            )
        )
        document.querySelector('head').appendChild(msViewportStyle)
    }
});


/*===================================
  ===  SMOOTH SCROLL NAVIGATION   ===
  =================================== */
jQuery(document).ready(function(){
  jQuery('#menu-primary a[href*="#"]:not([href="#"]), a.woocommerce-review-link[href*="#"]:not([href="#"]), a.post-comments[href*="#"]:not([href="#"])').bind('click',function () {
    var headerHeight;
    var hash    = this.hash;
    var idName  = hash.substring(1);    // get id name
    var alink   = this;                 // this button pressed
    // check if there is a section that had same id as the button pressed
    if ( jQuery('section [id*=' + idName + ']').length > 0 && jQuery(window).innerWidth() >= 767 ){
      jQuery('.current').removeClass('current');
      jQuery(alink).parent('li').addClass('current');
    }else{
      jQuery('.current').removeClass('current');
    }
    if ( jQuery(window).innerWidth() >= 767 ) {
      headerHeight = jQuery('.sticky-navigation').outerHeight();
    } else {
      headerHeight = 0;
    }
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = jQuery(this.hash);
      target = target.length ? target : jQuery('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        jQuery('html,body').animate({
          scrollTop: target.offset().top - headerHeight + 10
        }, 1200);
        return false;
      }
    }
  });


    jQuery(".inpage_scroll_btn").click(function(event) {
        var anchor = jQuery(this).attr('data-anchor');
        var offset2 = -60;
        if ( jQuery(anchor).length){
            jQuery('html, body').animate({
                scrollTop: jQuery(anchor).offset().top + offset2
            }, 1200);
        }
    });
});



/*=====================================================
  ===  NAVIGATION AND NAVIGATION VISIBLE ON SCROLL  ===
  =====================================================*/
function mainNav() {
    if(jQuery('.overlay-layer-nav').hasClass('sticky-navigation-open')){
        return false;
    }
    var top = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
    var topMenuClose    = -70;
    var topMenuOpen     = 0;
    if ( jQuery('.admin-bar').length>0 ) {
        $parallax_one_header_height = jQuery('.navbar').height();
        topMenuClose    = $parallax_one_header_height * -1;
        topMenuOpen     = 32;
    }
    if ( top > 40 )
        jQuery('.appear-on-scroll').stop().animate({
            "opacity": '1',
            "top": topMenuOpen
        });
    else jQuery('.appear-on-scroll').stop().animate({
        "top": topMenuClose,
        "opacity": '0'
    });
}

/* TOP NAVIGATION MENU SELECTED ITEMS */
function scrolled() {

    if ( jQuery(window).width() >= 751 ) {
        var prallax_one_scrollTop = jQuery(window).scrollTop();       // cursor position
        var headerHeight = jQuery('.sticky-navigation').outerHeight();   // header height
        var isInOneSection = 'no';                              // used for checking if the cursor is in one section or not
        // for all sections check if the cursor is inside a section
        jQuery("section").each( function() {
            var thisID = '#' + jQuery(this).attr('id');           // section id
            var parallax_one_offset = jQuery(this).offset().top;         // distance between top and our section
            var thisHeight  = jQuery(this).outerHeight();         // section height
            var thisBegin   = parallax_one_offset - headerHeight;                      // where the section begins
            var thisEnd     = parallax_one_offset + thisHeight - headerHeight;         // where the section ends  
            // if position of the cursor is inside of the this section
            if ( prallax_one_scrollTop >= thisBegin && prallax_one_scrollTop <= thisEnd ) {
                isInOneSection = 'yes';
                jQuery('.current').removeClass('current');
                jQuery('#menu-primary a[href$="' + thisID + '"]').parent('li').addClass('current');    // find the menu button with the same ID section
                return false;
            }
            if (isInOneSection == 'no') {
                jQuery('.current').removeClass('current');
            }
        });
    }

}

var timer;
jQuery(window).scroll(function(){

    mainNav();

    if ( timer ) clearTimeout(timer);
    timer = setTimeout(function(){
        scrolled();
    }, 500);

});

var window_width_old;
jQuery(document).ready(function(){
    window_width_old = jQuery('.container').width();
    if( window_width_old <= 462 ) {
        jQuery('.post-type-archive-product .products').parallaxonegridpinterest({columns: 1,selector: '.product', calcMin: false});
        jQuery('.cart-collaterals .products').parallaxonegridpinterest({columns: 1,selector: '.product', calcMin: false});
    } else if( window_width_old <= 750  ){
        jQuery('.post-type-archive-product .products').parallaxonegridpinterest({columns: 2,selector: '.product', calcMin: false});
        jQuery('.cart-collaterals .products').parallaxonegridpinterest({columns: 1,selector: '.product', calcMin: false});
    } else {
        jQuery('.post-type-archive-product .products').parallaxonegridpinterest({columns: 4,selector: '.product', calcMin: false});
        jQuery('.cart-collaterals .products').parallaxonegridpinterest({columns: 2,selector: '.product', calcMin: false});
    }
});

jQuery(window).resize(function() {
    if( window_width_old != jQuery('.container').outerWidth() ){
        window_width_old = jQuery('.container').outerWidth();
        if( window_width_old <= 462 ) {
            jQuery('.post-type-archive-product .products').parallaxonegridpinterest({columns: 1,selector: '.product', calcMin: false});
            jQuery('.cart-collaterals .products').parallaxonegridpinterest({columns: 1,selector: '.product', calcMin: false});
        } else if( window_width_old <= 750  ){
            jQuery('.post-type-archive-product .products').parallaxonegridpinterest({columns: 2,selector: '.product', calcMin: false});
            jQuery('.cart-collaterals .products').parallaxonegridpinterest({columns: 1,selector: '.product', calcMin: false});
        } else {
            jQuery('.post-type-archive-product .products').parallaxonegridpinterest({columns: 4,selector: '.product', calcMin: false});
            jQuery('.cart-collaterals .products').parallaxonegridpinterest({columns: 2,selector: '.product', calcMin: false});
        }
    }
});

(function ($, window, document, undefined) {
    var defaults = {
            columns:                3,
            selector:               'div',
            excludeParentClass:     '',
            calcMin:                true
        };
    function ParallaxOneGridPinterest(element, options) {
        this.element    = element;
        this.options    = $.extend({}, defaults, options);
        this.defaults   = defaults;
        this.init();
    }
    ParallaxOneGridPinterest.prototype.init = function () {
        var self            = this,
            $container      = $(this.element);
            $select_options = $(this.element).children();
        self.make_magic( $container, $select_options );
    };
    ParallaxOneGridPinterest.prototype.make_magic = function (container) {
        var self            = this;
            $container      = $(container),
            columns_height  = [],
            prefix          = 'parallax_one',
            unique_class    = prefix + '_grid_' + self.make_unique();
            local_class     = prefix + '_grid';
        var classname;
        var substr_index    = this.element.className.indexOf(prefix+'_grid_');
        if( substr_index>-1 ) {
            classname = this.element.className.substr( 0, this.element.className.length-unique_class.length-local_class.length-2 );
        } else {
            classname = this.element.className;
        }
        var my_id;
        if( this.element.id == '' ) {
            my_id = prefix+'_id_' + self.make_unique();
        } else {
            my_id = this.element.id;
        }
        $container.after('<div id="' + my_id + '" class="' + classname + ' ' + local_class + ' ' + unique_class + '"></div>');
        var i;
        for(i=1; i<=this.options.columns; i++){
            columns_height.push(0);
            var first_cols = '';
            var last_cols = '';
            if( i%self.options.columns == 1 ) { first_cols = prefix + '_grid_first'; }
            if( i%self.options.columns == 0 ) { first_cols = prefix + '_grid_last'; }
            $('.'+unique_class).append('<div class="' + prefix + '_grid_col_' + this.options.columns +' ' + prefix + '_grid_column_' + i +' ' + first_cols + ' ' + last_cols + '"></div>');
        }
        var calcMin = this.options.calcMin;
        var cols = this.options.columns;
        if( this.element.className.indexOf(local_class)<0 ){
            
            $container.children(this.options.selector).each(function(index){
                if(calcMin == true){
                    var min = Math.min.apply(null,columns_height);
                    var this_index = columns_height.indexOf(min)+1;
                }
                else {
                    this_index = index % cols + 1;
                }
                $(this).attr(prefix+'grid-attr','this-'+index).appendTo('.'+unique_class +' .' + prefix + '_grid_column_'+this_index);
                if(calcMin == true){
                    columns_height[this_index-1] = $('.'+unique_class +' .' + prefix + '_grid_column_'+this_index).height();
                }
                    
            });
            
        } else {
            var no_boxes = $container.find(this.options.selector).length;
            var i;
            for( i=0; i<no_boxes; i++ ){
                if(calcMin == true){
                    var min = Math.min.apply(null,columns_height);
                    var this_index = columns_height.indexOf(min)+1;
                }
                else {
                    this_index = i % cols + 1;
                }
                $('#'+this.element.id).find('['+prefix+'grid-attr="this-'+i+'"]').appendTo('.'+unique_class +' .' + prefix + '_grid_column_'+this_index);
                if(calcMin == true){
                    columns_height[this_index-1] = $('.'+unique_class +' .' + prefix + '_grid_column_'+this_index).height();
                }
            }
        }
        $container.remove();
    }
    
    ParallaxOneGridPinterest.prototype.make_unique = function () {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for( var i=0; i<10; i++ )
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return text;
    }
    
    ParallaxOneGridPinterest.prototype.allValuesSame = function(arr) {
        for(var i = 1; i < arr.length; i++){
            if(arr[i] !== arr[0])
                return false;
        }
        return true;
    }
    
    $.fn.parallaxonegridpinterest = function (options) {
        return this.each(function () {
            var value = '';
            if (!$.data(this, value)) {
                $.data(this, value, new ParallaxOneGridPinterest(this, options) );
            }
        });
    }
})(jQuery);

var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};



/*=====================================
========= ACCESSIBILITY READY =========
=======================================*/

// MENU NAVIGATION WITH ARROW KEYS 
( function( $ ) {

    
  $('.menu-item a').on('keydown', function(e) {
		// left key
		if(e.which === 37) {
			e.preventDefault();
			$(this).parent().prev().children('a').focus();
		}
		// right key
		else if(e.which === 39) {
			e.preventDefault();
			$(this).parent().next().children('a').focus();
		}
		// down key
		else if(e.which === 40) {
			e.preventDefault();
			if($(this).next().next().length){
				$(this).next().next().find('li:first-child a').first().focus();
			}
			else {
				$(this).parent().next().children('a').focus();
			}
		}
		// up key
		else if(e.which === 38) {
			e.preventDefault();
			if($(this).parent().prev().length){
				$(this).parent().prev().children('a').focus();
			}
			else {
                console.log($(this).parents('ul'));
				$(this).parents('ul').first().prev().prev().focus();
			}
		}
	});
} )( jQuery );


//ACCESSIBILITY MENU
( function( $ ) {

    function initMainNavigation( container ) {
        // Add dropdown toggle that display child menu items.
        container.find( '.menu-item-has-children > a' ).after( '<button class="dropdown-toggle" aria-expanded="false">' + screenReaderText.expand + '</button>' );

		// Toggle buttons and submenu items with active children menu items.
		container.find( '.current-menu-ancestor > button' ).addClass( 'toggled-on' );
		container.find( '.current-menu-ancestor > .sub-menu' ).addClass( 'toggled-on' );

		// Add menu items with submenus to aria-haspopup="true".
		container.find( '.menu-item-has-children' ).attr( 'aria-haspopup', 'true' );

		container.find( '.dropdown-toggle' ).click( function( e ) {
            var _this = $( this );
            e.preventDefault();
			_this.toggleClass( 'toggled-on' );
			_this.next( '.children, .sub-menu' ).toggleClass( 'toggled-on' );
			_this.attr( 'aria-expanded', _this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
			_this.html( _this.html() === screenReaderText.expand ? screenReaderText.collapse : screenReaderText.expand );
		});
    }
    
    initMainNavigation( $( '.main-navigation' ) );
    
    masthead = $( '#masthead' );
	menuToggle       = masthead.find( '#menu-toggle' );
	siteHeaderMenu   = masthead.find( '#site-header-menu' );
	siteNavigation   = masthead.find( '#site-navigation' ); 
    
    // Enable menuToggle.
	( function() {
		// Return early if menuToggle is missing.
        if ( ! menuToggle ) {
			return;
		}

		// Add an initial values for the attribute.
		menuToggle.click(function() {
			$( this ).add( siteHeaderMenu ).toggleClass( 'toggled-on' );
		} );
	} )();


    // Fix sub-menus for touch devices and better focus for hidden submenu items for accessibility.
	( function() {
        if ( ! siteNavigation || ! siteNavigation.children().length ) {
			return;
		}

		if ( 'ontouchstart' in window ) {
			siteNavigation.find( '.menu-item-has-children > a' ).on( 'touchstart.parallax-one', function( e ) {
				var el = $( this ).parent( 'li' );
				if ( ! el.hasClass( 'focus' ) ) {
					e.preventDefault();
					el.toggleClass( 'focus' );
					el.siblings( '.focus' ).removeClass( 'focus' );
				}
			} );
		}

		siteNavigation.find( 'a' ).on( 'focus.parallax-one blur.parallax-one', function() {
			$( this ).parents( '.menu-item' ).toggleClass( 'focus' );
		} );
	} )();


	// Add he default ARIA attributes for the menu toggle and the navigations.
	function onResizeARIA() {
		if ( 910 > window.innerWidth ) {
			if ( menuToggle.hasClass( 'toggled-on' ) ) {
				menuToggle.attr( 'aria-expanded', 'true' );
			} else {
				menuToggle.attr( 'aria-expanded', 'false' );
			}

			if ( siteHeaderMenu.hasClass( 'toggled-on' ) ) {
				siteNavigation.attr( 'aria-expanded', 'true' );
			} else {
				siteNavigation.attr( 'aria-expanded', 'false' );
			}

			menuToggle.attr( 'aria-controls', 'site-navigation social-navigation' );
		} else {
			menuToggle.removeAttr( 'aria-expanded' );
			siteNavigation.removeAttr( 'aria-expanded' );
			menuToggle.removeAttr( 'aria-controls' );
		}
	}
    
    $( document ).ready( function() {
		$( window ).on( 'load.parallax-one', onResizeARIA )
	} );
    
    
} )( jQuery );


/* mobile background fix */
jQuery( document ).ready( mobile_bg_fix );
function mobile_bg_fix() {
    if( isMobile.any() && jQuery( 'body.custom-background' ) ){
            bodyClass   = jQuery( 'body.custom-background' )
            imgURL      = bodyClass.css( 'background-image' );
            imgSize     = bodyClass.css( 'background-size' );
            imgPosition = bodyClass.css( 'background-position' );
            imgRepeat   = bodyClass.css( 'background-repeat' );
            jQuery( '#mobilebgfix' ).addClass( 'mobile-bg-fix-wrap' ).find( '.mobile-bg-fix-img' ).css( {
                'background-image'      : imgURL,
                'background-size'       : imgSize,
                'background-position'   : imgPosition,
                'background-repeat'     : imgRepeat
                } );
    }
}
