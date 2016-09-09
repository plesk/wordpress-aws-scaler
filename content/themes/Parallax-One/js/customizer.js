/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	
	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );
	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
			} else {
				$( '.site-title, .site-description' ).css( {
					'clip': 'auto',
					'color': to,
					'position': 'relative'
				} );
			}
		} );
	} );
	
	
	/***************************************
	******** HEADER SECTION *********
	****************************************/
	//Logo
	wp.customize("paralax_one_logo", function(value) {
        value.bind(function( to ) {
			if( to != '' ) {
				$( '.navbar-brand' ).removeClass( 'paralax_one_only_customizer' );
				$( '.header-logo-wrap' ).addClass( 'paralax_one_only_customizer' );
			}
			else {
				$( '.navbar-brand' ).addClass( 'paralax_one_only_customizer' );
				$( '.header-logo-wrap' ).removeClass( 'paralax_one_only_customizer' );
			}
				
            $(".navbar-brand img").attr( "src", to );
			
        } );
    });


	//Show Header Logo
	wp.customize('paralax_one_header_logo', function( value ){
		value.bind(function( to ) {
			if( to != '' ) {
				$('#parallax_header .only-logo').removeClass( 'paralax_one_only_customizer' );
			} else {
				$('#parallax_header .only-logo').addClass( 'paralax_one_only_customizer' );
			}
			$( '#parallax_header .only-logo img' ).attr('src', to);
		});
		
	});
	
	//Title
	wp.customize("parallax_one_header_title", function(value) {
		
        value.bind(function( to ) {
			if( to != '' ) {
				$( '#parallax_header .intro-section h2' ).removeClass( 'paralax_one_only_customizer' );
			}
			else {
				$( '#parallax_header .intro-section h2' ).addClass( 'paralax_one_only_customizer' );
			}
			$( '#parallax_header .intro-section h2' ).html( to );
	    } );
		
    });
	
	//Subtitle
	wp.customize("parallax_one_header_subtitle", function(value) {
		
        value.bind(function( to ) {
			if( to != '' ) {
				$( '#parallax_header .intro-section h5' ).removeClass( 'paralax_one_only_customizer' );
			} else {
				$( '#parallax_header .intro-section h5' ).addClass( 'paralax_one_only_customizer' );
			}
			$( '#parallax_header .intro-section h5' ).html( to );
		} );
		
    });
	
	//Button text
	wp.customize("parallax_one_header_button_text", function(value) {
		
        value.bind(function( to ) {

			if( to != '' ) {
				$( '#parallax_header .inpage_scroll_btn' ).removeClass( 'paralax_one_only_customizer' );
			} else {
				$( '#parallax_header .inpage_scroll_btn' ).addClass( 'paralax_one_only_customizer' );
			}
			$( '#parallax_header .inpage_scroll_btn' ).html( to );
		} );
		
    });


	//Button link
	wp.customize("parallax_one_header_button_link", function(value) {
		
        value.bind(function( to ) {
        	if( to.charAt(0) == '#' ){
        		$( '#parallax_header .inpage_scroll_btn' ).removeAttr('onClick');
				$( '#parallax_header .inpage_scroll_btn' ).attr( 'data-anchor', to );
        	} else{
        		$( '#parallax_header .inpage_scroll_btn' ).removeAttr('data-anchor');
        		$( '#parallax_header .inpage_scroll_btn' ).attr( 'onClick', 'parent.location=\''+to+'\'' );
        	}
		} );
		
    });	
	

	/******************************************************
	************* OUR STORY SECTION ****************
	*******************************************************/
	//Title
	wp.customize("parallax_one_our_story_title", function(value) {
		
        value.bind(function( to ) {
			
			if( to != '' ) {
				$( '.brief' ).removeClass( 'paralax_one_only_customizer' );
				$( '.brief .content-section h2' ).removeClass( 'paralax_one_only_customizer' );
				$( '.brief .content-section .colored-line-left').removeClass(  'paralax_one_only_customizer' );
				$( '.brief .content-section h2' ).text( to );
			}
			else {
				$( '.brief .content-section h2' ).addClass( 'paralax_one_only_customizer' );
				$( '.brief .content-section .colored-line-left').addClass(  'paralax_one_only_customizer' );
				if( $('.brief .brief-content-two').hasClass('paralax_one_only_customizer') && $('.brief .content-section .brief-content-text').hasClass('paralax_one_only_customizer') ){
					$( '.brief' ).addClass( 'paralax_one_only_customizer' );
				}
			}
	    } );
		
    });
	
	wp.customize("parallax_one_our_story_text",function(value) {
		
		value.bind(function( to ) {
			if( to != '' ) {
				$( '.brief' ).removeClass( 'paralax_one_only_customizer' );
				$( '.brief .content-section .brief-content-text' ).removeClass( 'paralax_one_only_customizer' );
				$( '.brief .content-section .brief-content-text' ).html( to );
			} else {
				$( '.brief .content-section .brief-content-text' ).addClass( 'paralax_one_only_customizer' );
				if( $( '.brief .content-section h2' ).hasClass('paralax_one_only_customizer') && $('.brief .brief-content-two').hasClass('paralax_one_only_customizer') ){
					$( '.brief' ).addClass( 'paralax_one_only_customizer' );
				}
			}
			
		});
		
	});
	
	wp.customize("paralax_one_our_story_image",function(value) {
		
		value.bind(function( to ) {
			if( to != '' ) {
				$( '.brief' ).removeClass( 'paralax_one_only_customizer' );
				$('.brief .brief-content-two').removeClass( 'paralax_one_only_customizer' );
				$( '.brief .brief-content-two .brief-image-right img' ).attr('src', to);
			} else {
				$('.brief .brief-content-two').addClass( 'paralax_one_only_customizer' );
				if( $( '.brief .content-section h2' ).hasClass('paralax_one_only_customizer') && $('.brief .content-section .brief-content-text').hasClass('paralax_one_only_customizer') ){
					$( '.brief' ).addClass( 'paralax_one_only_customizer' );
				}
			}
		});
		
	});

	/******************************************************
	*********** OUR SERVICES SECTION **************
	*******************************************************/
	
	
	//Title
	wp.customize("parallax_one_our_services_title", function(value) {
		
        value.bind(function( to ) {
			if( to != '' ) {
				$( '.services' ).removeClass( 'paralax_one_only_customizer' );
				$( '.services .section-header h2' ).removeClass( 'paralax_one_only_customizer' );
				$('.services .section-header .colored-line' ).removeClass( 'paralax_one_only_customizer' );
				$( '.services .section-header h2' ).text( to );
			}
			else {
				$( '.services .section-header h2' ).addClass( 'paralax_one_only_customizer' );
				$('.services .section-header .colored-line' ).addClass( 'paralax_one_only_customizer' );
				if($( '.services .section-header .sub-heading' ).hasClass('paralax_one_only_customizer') && isEmpty($('.parallax_one_grid_column_1')) && isEmpty($('.parallax_one_grid_column_2')) && isEmpty($('.parallax_one_grid_column_3')) ){
					$( '.services' ).addClass( 'paralax_one_only_customizer' );
				}
			}
	    } );
		
    });
	
	//Subtitle
	wp.customize("parallax_one_our_services_subtitle", function(value) {
		
        value.bind(function( to ) {
			if( to != '' ) {
				$( '.services' ).removeClass( 'paralax_one_only_customizer' );
				$( '.services .section-header .sub-heading' ).removeClass( 'paralax_one_only_customizer' );
				$( '.services .section-header .sub-heading' ).text( to );
			} else {
				$( '.services .section-header .sub-heading' ).addClass( 'paralax_one_only_customizer' );
				if($( '.services .section-header h2' ).hasClass('paralax_one_only_customizer')  && isEmpty($('.parallax_one_grid_column_1')) && isEmpty($('.parallax_one_grid_column_2')) && isEmpty($('.parallax_one_grid_column_3'))){
					$( '.services' ).addClass( 'paralax_one_only_customizer' );
				}
			}
		} );
		
    });

	
	/******************************************************
	*********** OUR TEAM SECTION **************
	*******************************************************/
	//Title
	wp.customize("parallax_one_our_team_title", function(value) {
		
        value.bind(function( to ) {
			
			if( to != '' ) {
				$( '.team' ).removeClass( 'paralax_one_only_customizer' );
				$( '.team .section-header h2' ).removeClass( 'paralax_one_only_customizer' );
				$( '.team .section-header .colored-line' ).removeClass( 'paralax_one_only_customizer' );
				$( '.team .section-header h2' ).text( to );
			} else {
				$( '.team .section-header h2' ).addClass( 'paralax_one_only_customizer' );
				$( '.team .section-header .colored-line' ).addClass( 'paralax_one_only_customizer' );
				if( $( '.team .section-header .sub-heading' ).hasClass( 'paralax_one_only_customizer' ) && isEmpty($('.team .team-member-wrap')) ){
					$( '.team' ).addClass( 'paralax_one_only_customizer' );
				}
			}
	    } );
		
    });
	
	//Subtitle
	wp.customize("parallax_one_our_team_subtitle", function(value) {
		
        value.bind(function( to ) {
			if( to != '' ) {
				$( '.team' ).removeClass( 'paralax_one_only_customizer' );
				$( '.team .section-header .sub-heading' ).removeClass( 'paralax_one_only_customizer' );
				$( '.team .section-header .sub-heading' ).text( to );
			} else {
				$( '.team .section-header .sub-heading' ).addClass( 'paralax_one_only_customizer' );
				if( $( '.team .section-header h2' ).hasClass('paralax_one_only_customizer') && isEmpty($('.team .team-member-wrap')) ){
					$( '.team' ).addClass( 'paralax_one_only_customizer' );
				}
			}
		} );
		
    });
	

	/******************************************************
	******** HAPPY CUSTOMERS SECTION ***********
	*******************************************************/
	//Title
	wp.customize("parallax_one_happy_customers_title", function(value) {
		
        value.bind(function( to ) {
			
			if( to != '' ) {
				$( '.testimonials' ).removeClass( 'paralax_one_only_customizer' );
				$( '.testimonials .section-header h2' ).removeClass( 'paralax_one_only_customizer' );
				$( '.testimonials .section-header .colored-line' ).removeClass( 'paralax_one_only_customizer' );
				$( '.testimonials .section-header h2' ).text( to );
			} else {
				$( '.testimonials .section-header h2' ).addClass( 'paralax_one_only_customizer' );
				$( '.testimonials .section-header .colored-line' ).addClass( 'paralax_one_only_customizer' );
				if( $( '.testimonials .section-header .sub-heading').hasClass('paralax_one_only_customizer') && isEmpty($('.testimonials .testimonials-wrap .parallax_one_grid_column_1')) && isEmpty($('.testimonials .testimonials-wrap .parallax_one_grid_column_2')) && isEmpty($('.testimonials .testimonials-wrap .parallax_one_grid_column_3'))){
					$( '.testimonials' ).addClass( 'paralax_one_only_customizer' );
				}
			}
	    } );
		
    });
	
	//Subtitle
	wp.customize("parallax_one_happy_customers_subtitle", function(value) {
		
        value.bind(function( to ) {
			if( to != '' ) {
				$( '.testimonials' ).removeClass( 'paralax_one_only_customizer' );
				$( '.testimonials .section-header .sub-heading' ).removeClass( 'paralax_one_only_customizer' );
				$( '.testimonials .section-header .sub-heading' ).text( to );
			} else {
				$( '.testimonials .section-header .sub-heading' ).addClass( 'paralax_one_only_customizer' );
				if( $( '.testimonials .section-header h2').hasClass('paralax_one_only_customizer') && isEmpty($('.testimonials .testimonials-wrap .parallax_one_grid_column_1')) && isEmpty($('.testimonials .testimonials-wrap .parallax_one_grid_column_2')) && isEmpty($('.testimonials .testimonials-wrap .parallax_one_grid_column_3')) ){
					$( '.testimonials' ).addClass( 'paralax_one_only_customizer' );
				}
			}
		} );
		
    });

	/******************************************************
	**************** RIBBON SECTION *****************
	*******************************************************/
	
	wp.customize( 'paralax_one_ribbon_background', function( value ) {
		value.bind( function( to ) {
			
			if ( '' != to ) {
				$( '.ribbon-wrap' ).attr( 'style','background-image:url('+to+')' );
			} else {
				$( '.ribbon-wrap' ).removeAttr('style');
			}
			
		} );
	} );	
	
	
	
	//Title
	wp.customize("parallax_one_ribbon_title", function(value) {
		
        value.bind(function( to ) {

			var button = wp.customize._value.parallax_one_button_text();

			if( to != '' ) {
				$( '.ribbon-wrap h2' ).removeClass( 'paralax_one_only_customizer' );
				$( '.ribbon-wrap' ).removeClass( 'paralax_one_only_customizer' );
			} else {
				$( '.ribbon-wrap h2' ).addClass( 'paralax_one_only_customizer' );
				if(button==''){
					$( '.ribbon-wrap' ).addClass( 'paralax_one_only_customizer' );
				}
			}
			$( '.ribbon-wrap h2' ).html( to );
		} );
		
    });
	
	
	//Button text
	wp.customize("parallax_one_button_text", function(value) {
		
        value.bind(function( to ) {
			var title = wp.customize._value.parallax_one_ribbon_title();
			if( to != '' ) {
				$( '.ribbon-wrap' ).removeClass( 'paralax_one_only_customizer' );
				$( '.ribbon-wrap button' ).removeClass( 'paralax_one_only_customizer' );

			} else {
				$( '.ribbon-wrap button' ).addClass( 'paralax_one_only_customizer' );
				if(title==''){
					$( '.ribbon-wrap' ).addClass( 'paralax_one_only_customizer' );
				}
			}
			$( '.ribbon-wrap button' ).html( to );
		} );
		
    });


	//Button link
	wp.customize("parallax_one_button_link", function(value) {
		
        value.bind(function( to ) {
			if( to.charAt(0) == '#' ){
				$( '#ribbon .inpage_scroll_btn' ).removeAttr('onClick');
				$( '#ribbon .inpage_scroll_btn' ).attr( 'data-anchor', to );
			} else {
				$( '#ribbon .inpage_scroll_btn' ).removeAttr('data-anchor');
				$( '#ribbon .inpage_scroll_btn' ).attr( 'onClick', 'parent.location=\''+to+'\'' );
			}
		} );
		
    });	
	
	
	/******************************************************
	************ LATEST NEWS SECTION ***************
	*******************************************************/
	
	//Title
	wp.customize("parallax_one_latest_news_title", function(value) {
		
        value.bind(function( to ) {

			if( to != '' ) {
				$( '.timeline .timeline-text' ).removeClass( 'paralax_one_only_customizer' );
			} else {
				$( '.timeline .timeline-text' ).addClass( 'paralax_one_only_customizer' );
			}
			$( '#latestnews .timeline-text h2' ).text( to );
		} );
		
    });
    
	
	/***************************************
	******** FOOTER SECTION *********
	****************************************/
	//Copyright
	wp.customize("parallax_one_copyright", function(value) {
        value.bind(function( to ) {
			if( to != '' ) {
				$( '.parallax_one_copyright_content' ).removeClass( 'paralax_one_only_customizer' );
			} else {
				$( '.parallax_one_copyright_content' ).addClass( 'paralax_one_only_customizer' );
			}
			
			$( '.parallax_one_copyright_content' ).text( to );
	    } );
    });
	
	function isEmpty( el ){
		return ($.trim(el.html()) === '' ? true : false);
	}
	
} )( jQuery );
