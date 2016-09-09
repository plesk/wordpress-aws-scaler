jQuery(document).ready(function() {
    
	/* Tabs in welcome page */
	function parallax_one_welcome_page_tabs(event) {
		jQuery(event).parent().addClass("active");
        jQuery(event).parent().siblings().removeClass("active");
        var tab = jQuery(event).attr("href");
        jQuery(".parallax-one-tab-pane").not(tab).css("display", "none");
        jQuery(tab).fadeIn();
	}
	
	var parallax_one_actions_anchor = location.hash;
	
	if( (typeof parallax_one_actions_anchor !== 'undefined') && (parallax_one_actions_anchor != '') ) {
		parallax_one_welcome_page_tabs('a[href="'+ parallax_one_actions_anchor +'"]');
	}
	
    jQuery(".parallax-one-nav-tabs a").click(function(event) {
        event.preventDefault();
		parallax_one_welcome_page_tabs(this);
    });
	
});