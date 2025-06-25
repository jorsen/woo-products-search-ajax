<?php
// Ajax callback: product search
function woo_products_search_ajax() {

	/* ------------------------------------------------
	 * 1. Bail if no search term
	 * ------------------------------------------------ */
	if ( empty( $_GET['s'] ) ) {
		wp_die();
	}

	$search_query = sanitize_text_field( wp_unslash( $_GET['s'] ) );

	/* ------------------------------------------------
	 * 2. Product query — first 5 suggestions + total
	 * ------------------------------------------------ */
	$product_q = new WP_Query( array(
		'post_type'      => 'product',
		'posts_per_page' => 5,
		's'              => $search_query,
		'fields'         => 'ids',
		'no_found_rows'  => false,          // we need found_posts
	) );

	$total_products = $product_q->found_posts;        // <-- we’ll reuse this later

	/* ------------------------------------------------
	 * 3. Build the HTML string
	 * ------------------------------------------------ */
	$results_html  = '<ul class="wps-product-results">';

	if ( $product_q->have_posts() ) {
		while ( $product_q->have_posts() ) {
			$product_q->the_post();
			$results_html .= sprintf(
				'<li><a href="%s">%s</a></li>',
				esc_url( get_permalink() ),
				esc_html( get_the_title() )
			);
		}
	} else {
		$results_html .= '<li>No products found</li>';
	}
	$results_html .= '</ul>';

	/* ------------------------------------------------
	 * 4. Show categories *only* when we have products
	 * ------------------------------------------------ */
	// Show categories only if there are matching products
if ( $total_products > 0 ) {

    // 1. Get all product categories (no filtering yet)
    $all_categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'orderby'    => 'name',
        'hide_empty' => false,
    ) );

    // 2. Break the search into words
    $query_words = preg_split( '/\s+/', $search_query, -1, PREG_SPLIT_NO_EMPTY );

    // 3. Filter categories: match any word in the name (case-insensitive)
    $categories = array_filter( $all_categories, function ( $cat ) use ( $query_words ) {
        $name = strtolower( $cat->name );
        foreach ( $query_words as $word ) {
            if ( strpos( $name, strtolower( $word ) ) !== false ) {
                return true;
            }
        }
        return false;
    } );

    // 4. Display categories if any match
    if ( ! empty( $categories ) ) {
        $results_html .= '<div class="wps-cat-title">Categories:</div>';
        $results_html .= '<ul class="wps-cat-results">';

        $index = 0;
        foreach ( $categories as $cat ) {
            $hidden = $index >= 5 ? ' style="display:none" class="wps-cat-hidden"' : '';
            $results_html .= sprintf(
                '<li%s><a href="%s">%s</a></li>',
                $hidden,
                esc_url( get_term_link( $cat ) ),
                esc_html( $cat->name )
            );
            $index++;
        }

        $results_html .= '</ul>';

        if ( $index > 5 ) {
            $results_html .= '<a href="#" id="wps-expand" class="wps-expand-link" data-step="5">Expand</a>';
        }
    }
}


	/* ------------------------------------------------
	 * 5. “Show All Products (N)” link
	 * ------------------------------------------------ */
	$results_html .= sprintf(
		'<a href="%s" class="show-all-button header-contact">Show All Products (%d)</a>',
		esc_url( home_url( '?s=' . rawurlencode( $search_query ) ) ),
		intval( $total_products )
	);

	wp_reset_postdata();
	echo $results_html;
	wp_die();
}
add_action( 'wp_ajax_custom_search_products',        'woo_products_search_ajax' );
add_action( 'wp_ajax_nopriv_custom_search_products', 'woo_products_search_ajax' );
