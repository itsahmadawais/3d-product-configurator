<?php
/*
Plugin Name: Woocommerce 3D Products
Plugin URI: https://www.rcreators.com/woocommerce-3d-products
Description: Create 3D products and add thier customize options. Show your products in 3D and give your users to change them real time with appereance options
Author: Rcreators Websolutions
Developer: Rishi Mehta // rishi@rcreators.com
Version: 2.6.0.1
Author URI: http://rcreators.com
WC tested up to: 4.5.2
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'TEXTDOMAIN', 'rc-3d' );

if ( ! defined( 'RC_3D' ) ) {
	define( 'RC_3D', true );
}

if ( ! defined( 'RC_3D_DIR' ) ) {
	define( 'RC_3D_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'RC_3D_INC' ) ) {
	define( 'RC_3D_INC', RC_3D_DIR . 'includes/' );
}

/* Construct wc 3D Plugin */
if( ! function_exists( 'rc_3d_constructor' ) ) {
	function rc_3d_constructor() {
		
		require_once( RC_3D_INC . 'classes/class.rc-3d.php' );
		require_once( RC_3D_INC . 'classes/class.rc-3d-template.php' );
		require_once( RC_3D_INC . 'classes/class.rc-3d-ajax.php' );
		
		/* Load Admin Class */
		if ( is_admin() ) {
			require_once(RC_3D_INC . 'classes/admin/admin-metabox.php');
			require_once( RC_3D_INC . 'classes/admin/options-pages-with-submenus.php' );
			require_once( RC_3D_INC . 'classes/admin/class.rc-3d-admin-init.php' );
		}
		
		// Lets Make 3D Products
		global $rc_3d;
		$rc_3d = RC_3D();


	}
}
add_action( 'rc_3d_init', 'rc_3d_constructor' );

/**
* Check if WooCommerce is active
**/
if( ! function_exists( 'rc_3d_install' ) ) {
	function rc_3d_install() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'rc_3d_install_woocommerce_admin_notice' );
		} else {
			do_action( 'rc_3d_init' );

		}
	}
}
add_action( 'plugins_loaded', 'rc_3d_install', 11 );

/* Woocomerce Deactive Notice */
if( ! function_exists( 'rc_3d_install_woocommerce_admin_notice' ) ) {
	function rc_3d_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php _e( 'Woocommerce 3D Products is enabled but not effective. It requires WooCommerce in order to work.', TEXTDOMAIN ); ?></p>
		</div>
	<?php
	}
}

/* Add Css & Js files on product edit page */
function add_admin_scripts( $hook ) {
	global $post;
	if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
		if ( 'product' === $post->post_type ) {
			wp_enqueue_style( 'wc_3d_admin_css', plugins_url('css/wc-3d-admin.css', __FILE__) );
		}
	}
}
add_action( 'admin_enqueue_scripts', 'add_admin_scripts', 10, 1 );


register_activation_hook(__FILE__, 'wc_3d_activation');

function wc_3d_activation($network_wide){

	$rc_wc_options = get_option( 'rc_wc_3d_products' );

	$fonts_data = $rc_wc_options['rc_wc_fonts'];

	if($fonts_data){

		$rc_wc_3d_fonts = array();

		$rc_wc_3d_fonts['rc_wc_fonts'] = $fonts_data;

		update_option('rc_wc_3d_fonts', $rc_wc_3d_fonts );

	}
}

/**
 * 🟢 Get fabric library data.
 */

add_action( 'wp_ajax_nopriv_get_fabric_library_data', 'fabric_library_handler' );
add_action( 'wp_ajax_get_fabric_library_data', 'fabric_library_handler' );

function fabric_library_handler() {
	$data = [];
	$args = [
		'post_type' => 'fabric_gallery',
		'posts_per_page' => -1
	];
	$fabric_gallery = get_posts($args);
	foreach($fabric_gallery as $item){
		//ID
		$post['ID'] = $item->ID;
		//Title
		$post['title'] = $item->post_title;
		$post['content'] = $item->post_content;
		//Image
		$post['image'] = get_post_meta($item->ID, 'ts-fabric-image-url');
		if($post['image'] && is_array($post['image'])){
			$post['image'] = $post['image'][0];
		}
		//Price
		$post['price'] = get_post_meta($item->ID, 'ts-fabric-price');
		if($post['price'] && is_array($post['price'])){
			$post['price'] = $post['price'][0];
		} else{
			$post['price'] = 0;
		}
		// Get All Related Patterns
		$patterns = wp_get_post_terms($item->ID,['pattern']);
		if(is_array($patterns) && count($patterns)>0){
			$patterns_array = [];
			foreach($patterns as $pattern){
				$patternData = [];
				$patternData['ID'] = $pattern->term_id;
				$patternData['title'] = $pattern->name;
				$patterns_array[] = $patternData;
			}
			$post['patterns'] = $patterns_array;
		}
		// Get All Related Brands
		$brands = wp_get_post_terms($item->ID,['color_brand']);
		if(is_array($brands) && count($brands)>0){
			$brands_array = [];
			foreach($brands as $brand){
				$brandData['ID'] = $brand->term_id;
				$brandData['title'] = $brand->name;
				$brandData['image'] = get_term_meta($brand->term_id, '_color_brand_img')[0];
				$brands_array[] = $brandData;
			}
			$post['brands'] = $brands_array;
		}
		// Get All Related Colors
		$colors = wp_get_post_terms($item->ID,['color']);
		if(is_array($colors) && count($colors)>0){
			$colors_array = [];
			foreach($colors as $color){
				$colorData['ID'] = $color->term_id;
				$colorData['title'] = $color->name;
				$color_code = get_term_meta($color->term_id, '_color_code')[0];
				$colorData['color'] = !empty($color_code) ? '#'.$color_code : '#ffffff';
				$colors_array[] = $colorData;
			}
			$post['colors'] = $colors_array;
		}


		$data[] = $post;
	}
	wp_send_json_success( $data );
}
?>