<?php
/**
 * Hook definitions
 *
 * Contains a list of all of Parallax One's hooks via which custom code can be inserted.
 */


# Prevent direct access to this file
if ( 1 == count( get_included_files() ) ) {
	header( 'HTTP/1.1 403 Forbidden' );
	return;
}

/**
 * Before HTML
 *
 * THA hook: tha_html_before
 */
 function parallax_hook_html_before() {
 	do_action( 'parallax_html_before' );
 	do_action( 'tha_html_before' );
 }

 /**
 * Top of head
 *
 * THA hook: tha_head_top
 */
function parallax_hook_head_top() {
	do_action( 'parallax_head_top' );
	do_action( 'tha_head_top' );
}

/**
 * Bottom of head
 *
 * THA hook: tha_head_bottom
 */
function parallax_hook_head_bottom() {
	do_action( 'parallax_head_bottom' );
	do_action( 'tha_head_bottom' );
}

/**
 * Top of body
 *
 * THA hook: tha_body_top
 */
function parallax_hook_body_top() {
	do_action( 'parallax_body_top' );
	do_action( 'tha_body_top' );
}

/**
 * Bottom of body
 *
 * THA hook: tha_body_bottom
 */
function parallax_hook_body_bottom() {
	do_action( 'parallax_body_bottom' );
	do_action( 'tha_body_bottom' );
}

/**
 * Before page header
 *
 * THA hook: tha_header_before
 */
function parallax_hook_header_before() {
	do_action( 'parallax_header_before' );
	do_action( 'tha_header_before' );
}

/**
 * Top of header
 *
 * THA hook: tha_header_top
 */
function parallax_hook_header_top() {
	do_action( 'parallax_header_top' );
	do_action( 'tha_header_top' );
}

/**
 * Bottom of header
 *
 * THA hook: tha_header_bottom
 */
function parallax_hook_header_bottom() {
	do_action( 'parallax_header_bottom' );
	do_action( 'tha_header_bottom' );
}

/**
 * After page header
 *
 * THA hook: tha_header_after
 */
function parallax_hook_header_after() {
	do_action( 'parallax_header_after' );
	do_action( 'tha_header_after' );
}

/**
 * Before content
 *
 * THA hook: tha_content_before
 */
function parallax_hook_content_before() {
	do_action( 'parallax_content_before' );
	do_action( 'tha_content_before' );
}


/**
 * After content
 *
 * THA hook: tha_content_after
 */
function parallax_hook_content_after() {
	do_action( 'parallax_content_after' );
	do_action( 'tha_content_after' );
}

/**
 * Top of content
 *
 * THA hook: tha_content_top
 */
function parallax_hook_content_top() {
	do_action( 'parallax_content_top' );
	do_action( 'tha_content_top' );
}

/**
 * Bottom of content
 *
 * THA hook: tha_content_bottom
 */
function parallax_hook_content_bottom() {
	do_action( 'parallax_content_bottom' );
	do_action( 'tha_content_bottom' );
}

/**
 * Hooks for heading section
 *
 */

 function parallax_hook_heading_before(){
   do_action( 'parallax_heading_before' );
 }

 function parallax_hook_heading_after(){
   do_action( 'parallax_heading_after' );
 }

 function parallax_hook_heading_top(){
   do_action( 'parallax_heading_top' );
 }

 function parallax_hook_heading_bottom(){
   do_action( 'parallax_heading_bottom' );
 }

/**
 * Hooks for logos section
 *
 */

 function parallax_hook_logos_before(){
   do_action( 'parallax_logos_before' );
 }

 function parallax_hook_logos_after(){
   do_action( 'parallax_logos_after' );
 }

 function parallax_hook_logos_top(){
   do_action( 'parallax_logos_top' );
 }

 function parallax_hook_logos_bottom(){
   do_action( 'parallax_logos_bottom' );
 }

/**
 * Hooks for services section
 *
 */
function parallax_hook_services_before() {
	do_action( 'parallax_services_before' );
}

function parallax_hook_services_after() {
	do_action( 'parallax_services_after' );
}

function parallax_hook_services_top() {
	do_action( 'parallax_services_top' );
}

function parallax_hook_services_bottom() {
	do_action( 'parallax_services_bottom' );
}

function parallax_hook_services_entry_top() {
	do_action( 'parallax_services_entry_top' );
}

function parallax_hook_services_entry_bottom() {
	do_action( 'parallax_services_entry_bottom' );
}

function parallax_hook_services_entry_before() {
	do_action( 'parallax_services_entry_before' );
}

function parallax_hook_services_entry_after() {
	do_action( 'parallax_services_entry_after' );
}

/**
 * Hooks for about section
 *
 */
function parallax_hook_about_before(){
  do_action( 'parallax_about_before' );
}

function parallax_hook_about_after(){
  do_action( 'parallax_about_after' );
}

function parallax_hook_about_top(){
  do_action( 'parallax_about_top' );
}

function parallax_hook_about_bottom(){
  do_action( 'parallax_about_bottom' );
}

/**
 * Hooks for team section
 *
 */

 function parallax_hook_team_before(){
   do_action( 'parallax_team_before' );
 }

 function parallax_hook_team_after(){
   do_action( 'parallax_team_after' );
 }

 function parallax_hook_team_top(){
   do_action( 'parallax_team_top' );
 }

 function parallax_hook_team_bottom(){
   do_action( 'parallax_team_bottom' );
 }

 /**
  * Hooks for testimonials section
  *
  */

  function parallax_hook_tetimonials_before(){
    do_action( 'parallax_tetimonials_before' );
  }

  function parallax_hook_tetimonials_after(){
    do_action( 'parallax_tetimonials_after' );
  }

  function parallax_hook_tetimonials_top(){
    do_action( 'parallax_tetimonials_top' );
  }

  function parallax_hook_tetimonials_bottom(){
    do_action( 'parallax_tetimonials_bottom' );
  }

  function parallax_hook_testimonials_entry_top() {
  	do_action( 'parallax_testimonials_entry_top' );
  }

  function parallax_hook_testimonials_entry_bottom() {
  	do_action( 'parallax_testimonials_entry_bottom' );
  }

  function parallax_hook_testimonials_entry_before() {
  	do_action( 'parallax_testimonials_entry_before' );
  }

  function parallax_hook_testimonials_entry_after() {
  	do_action( 'parallax_testimonials_entry_after' );
  }

  /**
   * Hooks for ribbon section
   *
   */

   function parallax_hook_ribbon_before(){
     do_action( 'parallax_ribbon_before' );
   }

   function parallax_hook_ribbon_after(){
     do_action( 'parallax_ribbon_after' );
   }

   function parallax_hook_ribbon_top(){
     do_action( 'parallax_ribbon_top' );
   }

   function parallax_hook_ribbon_bottom(){
     do_action( 'parallax_ribbon_bottom' );
   }

   /**
    * Hooks for news section
    *
    */

    function parallax_hook_news_before(){
      do_action( 'parallax_news_before' );
    }

    function parallax_hook_news_after(){
      do_action( 'parallax_news_after' );
    }

    function parallax_hook_news_top(){
      do_action( 'parallax_news_top' );
    }

    function parallax_hook_news_bottom(){
      do_action( 'parallax_news_bottom' );
    }

    function parallax_latest_news_cat(){
      return apply_filters( 'parallax_latest_news_cat', '' );
    }

    /**
     * Hooks for contact section
     *
     */

     function parallax_hook_contact_before(){
       do_action( 'parallax_contact_before' );
     }

     function parallax_hook_contact_after(){
       do_action( 'parallax_contact_after' );
     }

     function parallax_hook_contact_top(){
       do_action( 'parallax_contact_top' );
     }

     function parallax_hook_contact_bottom(){
       do_action( 'parallax_contact_bottom' );
     }

     function parallax_hook_contact_entry_top() {
       do_action( 'parallax_contact_entry_top' );
     }

     function parallax_hook_contact_entry_bottom() {
       do_action( 'parallax_contact_entry_bottom' );
     }

     function parallax_hook_contact_entry_before() {
       do_action( 'parallax_contact_entry_before' );
     }

     function parallax_hook_contact_entry_after() {
       do_action( 'parallax_contact_entry_after' );
     }

     /**
      * Hooks for map section
      *
      */

      function parallax_hook_map_before(){
        do_action( 'parallax_map_before' );
      }

      function parallax_hook_map_after(){
        do_action( 'parallax_map_after' );
      }

      function parallax_hook_map_entry_top() {
        do_action( 'parallax_map_entry_top' );
      }

      function parallax_hook_map_entry_bottom() {
        do_action( 'parallax_map_entry_bottom' );
      }

      /**
       * Before entry
       *
       * THA hook: tha_entry_before
       */
      function parallax_hook_entry_before() {
      	do_action( 'parallax_entry_before' );
      	do_action( 'tha_entry_before' );
      }
      /**
       * After entry
       *
       * THA hook: tha_entry_after
       */
      function parallax_hook_entry_after() {
      	do_action( 'parallax_entry_after' );
      	do_action( 'tha_entry_after' );
      }
      /**
       * Top of entry
       *
       * THA hook: tha_entry_top
       */
      function parallax_hook_entry_top() {
      	do_action( 'parallax_entry_top' );
      	do_action( 'tha_entry_top' );
      }
      /**
       * Bottom of entry
       *
       * THA hook: tha_entry_bottom
       */
      function parallax_hook_entry_bottom() {
      	do_action( 'parallax_entry_bottom' );
      	do_action( 'tha_entry_bottom' );
      }

    /**
     * Before page lists
     */
    function parallax_hook_page_before() {
    	do_action( 'parallax_page_before' );
    }
    /**
     * After page lists
     */
    function parallax_hook_page_after() {
    	do_action( 'parallax_page_after' );
    }
    /**
     * Top of page lists
     */
    function parallax_hook_page_top() {
    	do_action( 'parallax_page_top' );
    }
    /**
     * Bottom of page lists
     */
    function parallax_hook_page_bottom() {
    	do_action( 'parallax_page_bottom' );
    }

    /**
     * Before search results
     */
    function parallax_hook_search_before() {
    	do_action( 'parallax_search_before' );
    }
    /**
     * After search results
     */
    function parallax_hook_search_after() {
    	do_action( 'parallax_search_after' );
    }
    /**
     * Top of search results
     */
    function parallax_hook_search_top() {
    	do_action( 'parallax_search_top' );
    }
    /**
     * Bottom of search results
     */
    function parallax_hook_search_bottom() {
    	do_action( 'parallax_search_bottom' );
    }

    /**
     * Before comment section
     *
     * THA hook: `tha_comments_before`
     */
    function parallax_hook_comments_before() {
    	do_action( 'parallax_comments_before' );
    	do_action( 'tha_comments_before' );
    }

    /**
     * After comment section
     *
     * THA hook: `tha_comments_before`
     */
    function parallax_hook_comments_after() {
    	do_action( 'parallax_comments_after' );
    	do_action( 'tha_comments_after' );
    }

    /**
     * Top of comment section
     */
    function parallax_hook_comments_top() {
    	do_action( 'parallax_comments_top' );
    }

    /**
     * Bottom of comment section
     */
    function parallax_hook_comments_bottom() {
    	do_action( 'parallax_comments_bottom' );
    }

    /**
     * Before sidebar
     *
     * THA hook: tha_sidebars_before
     */
    function parallax_hook_sidebar_before() {
    	do_action( 'parallax_sidebar_before' );
    	do_action( 'tha_sidebars_before' ); # Pluralization is intentional
    }

    /**
     * After sidebar
     *
     * THA hook: tha_sidebars_after
     */
    function parallax_hook_sidebar_after() {
    	do_action( 'parallax_sidebar_after' );
    	do_action( 'tha_sidebars_after' ); # Pluralization is intentional
    }

    /**
     * Top of sidebar
     *
     * THA hook: tha_sidebar_top
     */
    function parallax_hook_sidebar_top() {
    	do_action( 'parallax_sidebar_top' );
    	do_action( 'tha_sidebar_top' );
    }

    /**
     * Bottom of sidebar
     *
     * THA hook: tha_sidebar_bottom
     */
    function parallax_hook_sidebar_bottom() {
    	do_action( 'parallax_sidebar_bottom' );
    	do_action( 'tha_sidebar_bottom' );
    }

		/**
		 * Before single
		 */
		function parallax_hook_single_before() {
			do_action( 'parallax_single_before' );
		}

		/**
		 * After single
		 */
		function parallax_hook_single_after() {
			do_action( 'parallax_single_after' );
		}

		/**
		 * Top of single
		 */
		function parallax_hook_single_top() {
			do_action( 'parallax_single_top' );
		}

		/**
		 * Bottom of single
		 */
		function parallax_hook_single_bottom() {
			do_action( 'parallax_single_bottom' );
		}

		/**
		 * Content of 404 pages
		 */
		function parallax_hook_404_content() {
			do_action( 'parallax_404_content' );
		}
 ?>
