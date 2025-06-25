<?php
/*
Plugin Name: Woo Products Search Ajax
Description: Adds an AJAX-powered search feature to WooCommerce with autocomplete, dynamic category filtering, and custom styling. Insert the shortcode [woo_products_search] where you want the search box to appear.
Version: 1.0
Author: Jorsen Mejia
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include necessary files for search functions and Ajax handling
require_once plugin_dir_path( __FILE__ ) . 'includes/search-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/search-ajax.php';

// Enqueue scripts and styles
function woo_products_search_enqueue_assets() {
    wp_enqueue_script( 'woo-products-search-js', plugin_dir_url( __FILE__ ) . 'assets/js/woo-products-search.js', array( 'jquery' ), null, true );
    wp_enqueue_style( 'woo-products-search-css', plugin_dir_url( __FILE__ ) . 'assets/css/woo-products-search.css' );

    // Localize script for passing AJAX URL
    wp_localize_script( 'woo-products-search-js', 'wp_vars', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    ) );
}
add_action( 'wp_enqueue_scripts', 'woo_products_search_enqueue_assets' );

// Register the shortcode
function woo_products_search_shortcode() {
    // Output the HTML for the search input, results container, and spinner
    ob_start();
    ?>
    <div class="woo-products-search-wrapper">
        <input type="text" id="custom-search-input" placeholder="Search for products..." />
        <div id="search-results"></div>
        <div id="loading-spinner" class="spinner" style="display: none;"></div> <!-- Hidden spinner -->
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'woo_products_search', 'woo_products_search_shortcode' );

