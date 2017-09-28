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
 * Removes products count after categories name
 */
add_filter( 'woocommerce_subcategory_count_html', 'woo_remove_category_products_count' );

function woo_remove_category_products_count() {
    return;
}

function storefront_remove_best_selling_products() {
	remove_action('homepage', 'storefront_best_selling_products', 70);
}
add_action('init', 'storefront_remove_best_selling_products');

function storefront_remove_my_account() {
	remove_action('storefront_header', 'storefront_secondary_navigation', 30);
}
add_action('init', 'storefront_remove_my_account');

function social_links() {
	echo '
		<nav class="secondary-navigation" role="navigation" aria-label="Secondary Navigation">
		    <div class="menu-secondary-navigation-container">
			    <ul id="menu-secondary-navigation" class="menu">
			        <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="#" target="_blank" title="Facebook"><img src="/drobov-shop/wp-content/themes/storefront/assets/images/facebook.png" width="40px" alt=""></a></li>
			        <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="#" target="_blank" title="Youtube-канал"><img src="/drobov-shop/wp-content/themes/storefront/assets/images/youtube.png" width="40px"  alt=""></a></li>
				</ul>
			</div>		    
		</nav>
	';
}

function storefront_add_social_header() {
	add_action('storefront_header', 'social_links', 30);
}
add_action('init', 'storefront_add_social_header');

add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );
function woo_rename_tabs( $tabs ) {
	$tabs['additional_information']['title'] = 'Характеристики';
	return $tabs;
}

//add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_country']);
	unset($fields['billing']['billing_address_1']);
	unset($fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_state']);
	unset($fields['billing']['billing_postcode']);
	return $fields;
}

function storefront_post( $args ) {
	$res = '';
	$the_post = & get_post( $dummy_id = 468 );
	if ($the_post) {
		$res = $the_post->post_content;
	}
	echo $res;
}

function storefront_add_post_home() {
	add_action( 'homepage', 'storefront_post', 70 );
}
add_action('init', 'storefront_add_post_home');

/**
 * Display the post header with a link to the single post
 *
 * @since 1.0.0
 */
function storefront_post_header() {
	?>
	<header class="entry-header">
		<?php
		if ( is_single() ) {
			the_title( '<h1 class="entry-title">', '</h1>' );
			storefront_posted_on();
		} else {

			the_title( sprintf( '<h2 class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
			if ( 'post' == get_post_type() ) {
				storefront_posted_on();
			}
		}
		?>
	</header><!-- .entry-header -->
	<?php
}

add_filter('woocommerce_thankyou_order_received_text', 'woo_my_thankyou_order_received_text' );
function woo_my_thankyou_order_received_text() {
	return "Спасибо, ваш заказ принят. Мы скоро свяжемся с вами!";
}