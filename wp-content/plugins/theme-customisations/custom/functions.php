<?php
/**
 * Functions.php
 *
 * @package  Theme_Customisations
 * @author   WooThemes
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * functions.php
 * Add PHP snippets here
 */

## Удаляет "Рубрика: ", "Метка: " и т.д. из заголовка архива
add_filter('get_the_archive_title', function( $title ){
    return preg_replace('~^[^:]+: ~', '', $title );
});

function storefront_post_meta() {
    return false;
}

function remove_storefront_style() {
    set_theme_mod('storefront_styles', '');
    set_theme_mod('storefront_woocommerce_styles', '');
}

add_action( 'init', 'remove_storefront_style' );

/**
 * Display Product Categories
 * Hooked into the `homepage` action in the homepage template
 *
 * @since  1.0.0
 * @param array $args the product section args.
 * @return void
 */
function storefront_product_categories( $args ) {

    if ( storefront_is_woocommerce_activated() ) {

        $args = apply_filters( 'storefront_product_categories_args', array(
            'columns' 			=> 4,
            'child_categories' 	=> 0,
            'orderby' 			=> 'name',
            'title'				=> __( 'Shop by Category', 'storefront' ),
        ) );

        $shortcode_content = storefront_do_shortcode( 'product_categories', apply_filters( 'storefront_product_categories_shortcode_args', array(
            'number'  => intval( $args['limit'] ),
            'columns' => intval( $args['columns'] ),
            'orderby' => esc_attr( $args['orderby'] ),
            'parent'  => esc_attr( $args['child_categories'] ),
        ) ) );

        /**
         * Only display the section if the shortcode returns product categories
         */
        if ( false !== strpos( $shortcode_content, 'product-category' ) ) {

            echo '<section class="storefront-product-section storefront-product-categories" aria-label="' . esc_attr__( 'Product Categories', 'storefront' ) . '">';

            do_action( 'storefront_homepage_before_product_categories' );

            echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

            do_action( 'storefront_homepage_after_product_categories_title' );

            echo $shortcode_content;

            do_action( 'storefront_homepage_after_product_categories' );

            echo '</section>';

        }
    }
}

/*
 *
 * Removes products count after categories name
 *
 */
add_filter( 'woocommerce_subcategory_count_html', 'woo_remove_category_products_count' );

function woo_remove_category_products_count() {
    return;
}