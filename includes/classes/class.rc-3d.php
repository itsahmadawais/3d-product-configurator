<?php
/**
 * Main class
 *
 * @author Rcreators Websolutions
 * @package Woocommerce 3D products
 * @version 1.0.0
 */

if ( ! defined( 'RC_3D' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'RC_3D' ) ) {
	/**
	 * WooCommerce 3D Products
	 *
	 * @since 1.0.0
	 */
	class RC_3D {
		/**
		 * Single instance of the class
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \RC_3D
		 * @since 2.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self( $_REQUEST );
			}

			return self::$instance;
		}
		
		/**
		 * Constructor.
		 * 
		 * @param array $details
		 * @return \RC_3D
		 * @since 1.0.0
		 */
		public function __construct( $details ) {
			// set details for actions
			$this->details = $details;

			if( is_admin() ){
				$this->rc_3d_admin_init = RC_3D_Admin_Init();
			}
			add_filter('upload_mimes', array( $this, 'rc_3d_mime_types' ));

			add_filter( 'woocommerce_locate_template', array( $this, 'wc_3d_woocommerce_locate_template'), 20, 3 );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'wc_3d_add_mat_options_to_cart_item'), 10, 3 );
			add_filter( 'woocommerce_get_item_data', array( $this, 'wc_3d_display_mat_options_cart'), 10, 2 );

			add_action( 'wp_enqueue_scripts', array( $this, 'wc_3d_load_scripts'), 99 );
			add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'wc_3d_add_mat_options_to_order_items'), 10, 4 );

			/* Select Option for 3D product in Shop Loop */
			remove_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_add_to_cart', 10 );
			add_action('woocommerce_after_shop_loop_item', array( $this, 'wc_3d_woocommerce_template_loop_add_to_cart'), 10);

			/* Custom Price Cart */
			add_action('woocommerce_before_calculate_totals', array( $this, 'wc_3d_add_custom_total_price'), 10);

			/* Custom Inage Cart */
			add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'custom_new_product_image'), 10, 3 );

			/* Custom Image Order */
			add_filter( 'woocommerce_order_item_thumbnail', array( $this, 'custom_new_product_order_image'), 10, 2 );

			/* Custom Image Order Admin */
			add_filter( 'woocommerce_admin_order_item_thumbnail', array( $this, 'custom_woocommerce_admin_order_item_thumbnail'), 10, 3 );

			add_action( 'wp_head', array( $this, 'rc_3d_print_custom_css'), 900);

		}

		function rc_3d_print_custom_css(){
			
			$rc_wc_design_options = get_option( 'rc_wc_3d_design' );
			$heading_color = $rc_wc_design_options['mat_title_text'];
			$heading_bg = $rc_wc_design_options['mat_title_bg'];
			?>
			<style type="text/css">
				.wc_config_title { background: <?php echo $heading_bg; ?>; color: <?php echo $heading_color; ?>; }
				.ui-icon-triangle-1-e { border-top: 10px solid <?php echo $heading_color; ?>; }
				.ui-icon-triangle-1-s { border-bottom: 10px solid <?php echo $heading_color; ?>; }
			</style>
		<?php }
	

		function custom_new_product_image( $_product_img, $cart_item, $cart_item_key ) {
			if(!isset($cart_item['wc3dimageurl']) || empty($cart_item['wc3dimageurl']))
				return $_product_img;
			
			return '<img src="'.$cart_item['wc3dimageurl'].'" />';
			
		}

		function custom_new_product_order_image($var, $item){
			if(!isset($item['_wc_3d_custom_image']) || empty($item['_wc_3d_custom_image']))
				return $var;
			
			return '<img src="'.$item['_wc_3d_custom_image'].'" />';
		}

		// overwrite thumbnails of product in order in admin
		function custom_woocommerce_admin_order_item_thumbnail( $product_get_image_thumbnail_array_title_false, $item_id, $item ) {
			$img_url = wc_get_order_item_meta($item_id, '_wc_3d_custom_image', true);
			
			if($img_url == '')
				return $product_get_image_thumbnail_array_title_false;
			
			return '<img src="'.$img_url.'" />';
		}        

		/* Let Wordpress media upload Obj and Fbx files */
		public function rc_3d_mime_types($mimes){
			$mimes['dae'] = 'application/xml';
			$mimes['fbx'] = 'application/octet-stream';
			$mimes['FBX'] = 'application/octet-stream';
			$mimes['json'] = 'text/json';
			$mimes['json'] = 'application/json';
			$mimes['svg'] = 'image/svg+xml';
			$mimes['glb']  = 'application/octet-stream';
			$mimes['gltf']  = 'text/plain';
			$mimes['bin'] = 'application/octet-stream';
			$mimes['hdr'] = 'application/octet-stream';
			return $mimes;
		}

		function wc_3d_woocommerce_template_loop_add_to_cart() {
			
			global $product;

			$args = array();
			
			$defaults = array(
				'quantity'   => 1,
				'class'      => implode(
					' ',
					array_filter(
						array(
							'button',
							'product_type_' . $product->get_type(),
							$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
							$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
						)
					)
				),
				'attributes' => array(
					'data-product_id'  => $product->get_id(),
					'data-product_sku' => $product->get_sku(),
					'aria-label'       => $product->add_to_cart_description(),
					'rel'              => 'nofollow',
				),
			);

			$args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

			if ( isset( $args['attributes']['aria-label'] ) ) {
				$args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
			}

			$prefix = 'wc_3d_';
			//Get WC 3D Active post meta
			$wc_3d_activate = get_post_meta($product->get_id(), $prefix . 'activate', true);

			if($wc_3d_activate == 'on') {
			
				$link = $product->get_permalink();
				$class = esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' );
				echo '<a class="' . esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ) . '" href="' . esc_attr($link) . '">Select Options</a>';

			} else {

				echo apply_filters( 'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
					sprintf( '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
						esc_url( $product->add_to_cart_url() ),
						esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
						esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
						isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
						esc_html( $product->add_to_cart_text() )
					),
				$product, $args );
			}

		}

		function wc_3d_add_custom_total_price( $cart_object ) {
			
			if ( is_admin() && ! defined( 'DOING_AJAX' ) )
				return;

			$rc_wc_options = get_option( 'rc_wc_3d_products' );

			$use_price_option = $rc_wc_options['rc_wc_use_price_option'];

			if($use_price_option != 'yes')
				return;

			$custom_price = 0;
			
			foreach ( $cart_object->get_cart() as $key => $value ) {

				if(!isset($value["finalPrice"]))
					continue;

				$newValue = ($value["finalPrice"]);
				$value['data']->set_price( $newValue );
					
			}
		   
		}
		
		/* Load Three Js required script on single product page */
		public function wc_3d_load_scripts() {
			global  $woocommerce, $post;
			//Post Meta Prefix
			$prefix = 'wc_3d_';
			//Getting Post Id From Shortcode
			if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'product_page') ) {
				
				$string = $post->post_content;
				$start = '[product_page';
				$end = ']';
				
				$string = ' ' . $string;
				$ini = strpos($string, $start);
				if ($ini == 0) return '';
				$ini += strlen($start);
				$len = strpos($string, $end, $ini) - $ini;
				$shortcode_attribute = substr($string, $ini, $len);             
				$shortcode_attribute = str_replace('"','',$shortcode_attribute);
				$dirs = explode('=', $shortcode_attribute);
				
				$post_id = $dirs[1];
				
			}

			if ( !isset($post_id) )
				$post_id = $post->ID;
			
			//Get WC 3D Active post meta
			$wc_3d_activate = get_post_meta($post_id , $prefix . 'activate', true);

			$wc_3d_debug_activate = get_post_meta($post_id , $prefix . 'debug_activate', true);

			//Checking if WC 3D is activated on product or not
			if($wc_3d_activate == 'on') {
				//Default Scripts Which needed compulsory.
				wp_enqueue_script( 'Inflat-js', plugins_url() . '/woocommerce-3d-products/js/inflate.min.js','','', true );

				wp_enqueue_script( 'three-js', plugins_url() . '/woocommerce-3d-products/js/three.min.js','','', true );
				if($wc_3d_debug_activate == 'on')
					wp_enqueue_script( 'stats-js', plugins_url() . '/woocommerce-3d-products/js/stats.min.js','','', true );   

				wp_enqueue_script( 'detector-js', plugins_url() . '/woocommerce-3d-products/js/Detector.js','','', true );
				
				//Checking Which Object Type is loading and enqueue script based on it.
				$wc_3d_object_type = get_post_meta($post_id , $prefix . '3d_object_type', true);
				if($wc_3d_object_type == 'fbx') {
					wp_enqueue_script( 'FBXLoader-js', plugins_url() . '/woocommerce-3d-products/js/FBXLoader.js','','', true );
				} else if($wc_3d_object_type == 'dae') {
					wp_enqueue_script( 'ColladaLoader-js', plugins_url() . '/woocommerce-3d-products/js/ColladaLoader.js','','', true );
				} else if($wc_3d_object_type == 'gltf') {
					wp_enqueue_script( 'GltfLoader-js', plugins_url() . '/woocommerce-3d-products/js/GLTFLoader.js','','', true );
				}
				
				wp_enqueue_script( 'RGBELoader-js', plugins_url() . '/woocommerce-3d-products/js/RGBELoader.js','','', true );

				//Loading Camera Control Based on Backend Selection
				wp_enqueue_script( 'orbitControl-js', plugins_url() . '/woocommerce-3d-products/js/OrbitControls.js','','', true );

				//Loading Jquery UI
				wp_enqueue_script('jquery-ui-accordion');

				$wp_scripts = wp_scripts();

				//Loading Font-Awesome Js
				wp_enqueue_script( 'Font-Awesome', '//kit.fontawesome.com/e7507315b5.js','','', true );

				//DropZone Uploading File
				wp_enqueue_script( 'DropZone-js', plugins_url() . '/woocommerce-3d-products/js/dropzone.min.js','','', true );

				//Loading Product Showing Logic Js
				if($wc_3d_debug_activate == 'on') {
					wp_enqueue_script( 'wc-3d-product-js', plugins_url() . '/woocommerce-3d-products/js/wc-3d-product.js','','', true );
				}else {
					wp_enqueue_script( 'wc-3d-product-js', plugins_url() . '/woocommerce-3d-products/js/wc-3d-product.min.js','','', true );
				}

				wp_enqueue_script( 'wc-3d-loader', plugins_url() . '/woocommerce-3d-products/js/wc-3d-loader.js','','', true );
				
				wp_enqueue_script('wc-fabric-gallery-picker', plugins_url().'/woocommerce-3d-products/js/fabric-gallery.js', ['jquery']);
				
				wp_localize_script('wc-fabric-gallery-picker', 'frontendajax', array(
					'siteUrl' => get_template_directory_uri(),
					'ajaxUrl' => admin_url("admin-ajax.php")
				));
				$rc_wc_options = get_option( 'rc_wc_3d_products' );

				$use_price_option = 'yes';

				if(isset($rc_wc_options['rc_wc_use_price_option']))
					$use_price_option = $rc_wc_options['rc_wc_use_price_option'];

				$cast_shadow_option = 'yes';

				if(isset($rc_wc_options['rc_wc_cast_shadow']))
					$cast_shadow_option = $rc_wc_options['rc_wc_cast_shadow'];

				$allow_camera_downside = 'yes';

				if(isset($rc_wc_options['rc_wc_allow_downside']))
					$allow_camera_downside = $rc_wc_options['rc_wc_allow_downside'];

				wp_localize_script('wc-3d-product-js', 'wc3dProductJs', array(
					'siteUrl' => get_template_directory_uri(),
					'ajaxUrl' => admin_url("admin-ajax.php"),
					'cur_symbol' => get_woocommerce_currency_symbol(),
					'upload'=>admin_url( 'admin-ajax.php?action=handle_dropped_media' ),
					'delete'=>admin_url( 'admin-ajax.php?action=handle_deleted_media' ),
					'use_price'=>$use_price_option,
					'cast_shadow'=>$cast_shadow_option,
					'allow_downside'=>$allow_camera_downside,
				));

				//Loading CSS file
				wp_enqueue_style( 'DropZone_css', plugins_url() . '/woocommerce-3d-products/css/dropzone.min.css');

				$fonts_data = $rc_wc_options['rc_wc_fonts'];

				if(isset($fonts_data)){
					foreach ($fonts_data as $key => $value) {
						wp_enqueue_style( $value['font_id'], $value['font_css']);
					}
				}

				wp_enqueue_style( 'wc_3d_frontend_css', plugins_url() . '/woocommerce-3d-products/css/wc-3d-product-frontend.css');
				wp_enqueue_style( 'wc_3d_frontend_fabric_css', plugins_url() . '/woocommerce-3d-products/css/style.css');
				wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css');
				
			}
		}

		//Load Plugin template if product have wc 3D activated and object file selected
		public function wc_3d_woocommerce_locate_template( $template, $template_name, $template_path ) {
		   global  $woocommerce, $post;
			//Post Meta Prefix
			$prefix = 'wc_3d_';
			//Getting Post Id
			$post_id = $post->ID;

			//Get WC 3D Active post meta
			$wc_3d_activate = get_post_meta($post_id , $prefix . 'activate', true);

			//Checking if WC 3D is activated on product or not, if activated load or template or load theme / plugin default template
			if($wc_3d_activate == 'on') {

				if ($template_name == 'single-product/product-image.php') {
					$template  = RC_3D_DIR . 'woocommerce/single-product/product-image.php';
				}
			}

			return $template;
		}

		//Add Selected Option to Cart
		function wc_3d_add_mat_options_to_cart_item( $cart_item_data, $product_id, $variation_id ) {
			if(!isset($_POST['wc_3d_option']))
				return $cart_item_data;

			$wc_3d_mat_options = $_POST['wc_3d_option'];
		 
			if ( empty( $wc_3d_mat_options ) )
				return $cart_item_data;

			$rc_wc_options = get_option( 'rc_wc_3d_products' );

			$use_price_option = $rc_wc_options['rc_wc_use_price_option'];
		 
			$cart_item_data['wc_3d_mat_options'] = wc_clean($wc_3d_mat_options);

			if($use_price_option == 'yes')
				$cart_item_data['finalPrice'] = wc_clean($_POST["finalPrice"]);

			$cart_item_data['wc3dimageurl'] = $_POST["wc3dimageurl"];

			$cart_item_data['wc3dsvgurl'] = $_POST["wc3dsvgurl"];
		 
			return $cart_item_data;
		}

		function wc_3d_display_mat_options_cart( $item_data, $cart_item ) {
			if (!isset($cart_item['wc_3d_mat_options']) || empty( $cart_item['wc_3d_mat_options'] ) )
				return $item_data;

			$wc_3d_mat_options = $cart_item['wc_3d_mat_options'];

			foreach ($wc_3d_mat_options as $key => $value) {
				if(wc_clean($value) != "" && wc_clean($value) != "0" && wc_clean($value) != "Default"){
					$item_data[] = array(
						'key'     => $key,
						'value'   => wc_clean( $value ),
						'display' => '',
					);
				}
			}
		 
			return $item_data;
		}
		
		function wc_3d_add_mat_options_to_order_items( $item, $cart_item_key, $values, $order ) {
			if ( empty( $values['wc_3d_mat_options'] ) )
				return;

			$wc_3d_mat_options = $values['wc_3d_mat_options'];

			foreach ($wc_3d_mat_options as $key => $value) {
				if(wc_clean($value) != "" && wc_clean($value) != "0" && wc_clean($value) != "Default"){
					$item->add_meta_data( __( $key, TEXTDOMAIN ), wc_clean( $value ) );
				}
			}

			$item->add_meta_data( __( '_wc_3d_custom_image', TEXTDOMAIN ), wc_clean( $values['wc3dimageurl'] ) );
			$item->add_meta_data( __( '_wc_3d_custom_svg', TEXTDOMAIN ), wc_clean( $values['wc3dsvgurl'] ) );
			
		}

	}
}

/**
 * Unique access to instance of RC_3D class
 *
 * @return \RC_3D
 * @since 2.0.0
 */
function RC_3D(){
	return RC_3D::get_instance();
}