jQuery(document).ready(function($){

	/* prepend menu icon */
	$('#menu-icon').prepend('Navigate Here');
	
	/* toggle nav */
	$("#menu-icon").on("click", function(){
		$("#main-menu").slideToggle();
		$(this).toggleClass("active");
	});

	//Scroll To top
	jQuery(window).scroll(function(){
		if (jQuery(this).scrollTop() > 100) {
			jQuery('#gototop').css({bottom:"160px"});
		} else {
			jQuery('#gototop').css({bottom:"-100px"});
		}
	});
	jQuery('#gototop').click(function(){
		jQuery('html, body').animate({scrollTop: '0px'}, 1800);
		return false;
	});
	
	//Menus
	jQuery('#main-nav ul > li > ul, #main-nav ul > li > ul > li > ul, #main-nav ul > li > ul > li > ul> li > ul').parent('li').addClass('parent-list');
	
	jQuery("#main-nav li").each(function(){	
		var $sublist = jQuery(this).find('ul:first');		
		jQuery(this).hover(function(){	
			$sublist.stop().css({overflow:"hidden", height:"auto", display:"none"}).slideDown(600, function(){
				jQuery(this).css({overflow:"visible", height:"auto"});
			});	
		},
		function(){
		if($(window).width() >= 1024){

			$sublist.stop().slideUp(200, function()	{	
				jQuery(this).css({overflow:"hidden", display:"none"});
			});
		}});	
	});
	
// Add any other social script without script tags.

(function(doc, script) {
    var js, fjs = doc.getElementsByTagName(script)[0],
        frag = doc.createDocumentFragment(),
        add = function(url, id) {
            if (doc.getElementById(id)) {
                return;
            }
            js = doc.createElement(script);
            js.src = url;
            id && (js.id = id);
            frag.appendChild(js);
        };
    // Google+ button
    add('http://apis.google.com/js/plusone.js');
    // Facebook SDK
    add('http://connect.facebook.net/en_US/all.js#xfbml=1&appId=300097030090548', 'facebook-jssdk');
    // Twitter SDK
    add('http://platform.twitter.com/widgets.js');
    //Stumble Upon
	add('http://platform.stumbleupon.com/1/widgets.js');

    fjs.parentNode.insertBefore(frag, fjs);
}(document, 'script'));
	
});