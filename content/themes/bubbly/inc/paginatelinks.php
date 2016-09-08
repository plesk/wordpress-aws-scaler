<?php

/**
/*------------------------------*/
function bubbly_wp_pagination() {
global $wp_query;
$big = 12345678;
$page_format = paginate_links( array(
    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
    'format' => '?paged=%#%',
    'current' => max( 1, get_query_var('paged') ),
    'total' => $wp_query->max_num_pages,
    'type'  => 'array'
) );
if( is_array($page_format) ) {
            $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
            echo '<div>';
           
            foreach ( $page_format as $page ) {
                    echo "$page";
            }
			 echo '<span class="pages">' . sprintf(__('Page %d of ', 'bubbly'), $paged ). $wp_query->max_num_pages .'</span>';
           echo '</div>';
}
}

?>