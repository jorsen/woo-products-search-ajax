<?php
// Modify the WooCommerce search query
function woo_products_search_query( $query ) {
    // Check if it's a product search and it's the main query
    if ( ! is_admin() && $query->is_main_query() && is_search() && isset( $_GET['s'] ) ) {
        // Ensure it only searches products
        $query->set( 'post_type', 'product' );
        $query->set( 'posts_per_page', -1 );    // Limit the results
        $query->set( 'orderby', 'relevance' );  // Optional: Sort by relevance
    }
}
add_action( 'pre_get_posts', 'woo_products_search_query' );