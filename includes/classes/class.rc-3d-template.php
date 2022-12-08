<?php
/**
 * Template class
 *
 * @author Rcreators Websolutions
 * @package Woocommerce 3D products
 * @version 1.0.0
 */

if ( !defined( 'RC_3D' ) ) {
	exit;
} // Exit if accessed directly


if ( !class_exists( 'RC_3D_Template' ) ) {

	/**
	 * WooCommerce 3D Products
	 *
	 * @since 1.0.0
	 */
	class RC_3D_Template extends RC_3D {

		public function __construct() {
			// set details for actions
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'wc_3d_frontend_options' ), 10 );

			/* Localaise Tempalte for each config Type */
			add_action('wc_3d_text_template', array( $this, 'wc_3d_text_options_template' ), 10, 1 );
			add_action('wc_3d_gradient_template', array( $this, 'wc_3d_gradient_options_template' ), 10, 1 );
			add_action('wc_3d_img_template', array( $this, 'wc_3d_img_options_template' ), 10, 1 );
			add_action('wc_3d_pattern_template', array( $this, 'wc_3d_pattern_options_template' ), 10, 1 );
			add_action('wc_3d_object_template', array( $this, 'wc_3d_object_options_template' ), 10, 1 );
			add_action('wc_3d_material_template', array( $this, 'wc_3d_material_options_template' ), 10, 1 );
			add_action('wc_3d_choices_template', array( $this, 'wc_3d_choices_options_template' ), 10, 1 );
			add_action('wc_3d_fabricgallery_template', array( $this, 'wc_3d_fabricgallery_options_template' ), 10, 1 );

		}

		//Frontend Text ConfigType Template
		function wc_3d_text_options_template($mat_group = array()){

			$html = '<div class="material_group">';

			$mat_obj_title = $obj_mat_id = '';

			$obj_material_option = array();

			if ( isset( $mat_group['mat_obj_title' ] ) ) 
				$mat_obj_title = esc_html( $mat_group['mat_obj_title' ] );

			if ( isset( $mat_group['obj_mat_id' ] ) )
				$obj_mat_id = $mat_group['obj_mat_id' ];

			if ( !empty($mat_obj_title) )
				$html .= '<h3 class="wc_config_title">' .$mat_obj_title . '<span class="option_price"></span></h3>';

			$html .= '<div class="material_options text_color">';

			$html .= '<input type="text" placeholder="Write Text Here" class="field_text" data-text-id="'.$obj_mat_id.'" />';

			if(isset($mat_group['text_options']))
				$text_options = $mat_group['text_options'];

			if(!empty($text_options)){

				$html .= '<div class="text_options_block">';

				$html .= '<h5 class="text_options_heading">Text Options:</h5>';

				if(in_array('fontSize', $text_options)){

					$html .= '<div class="option_block_wrapper">';

					$html .= '<select class="font_size" data-text-id="'.$obj_mat_id.'">
					<option value="">Select Font Size</option>
					<option value="200px">200px</option>
					<option value="150px">150px</option>
					<option value="100px">100px</option>
					<option value="72px">72px</option>
					<option value="64px">64px</option>
					<option value="42px">42px</option>
					<option value="32px">32px</option>
					<option value="24px">24px</option>
					<option value="16px">16px</option>
					<option value="12px">12px</option>
					</select>';

					$html .= '<input type="hidden" class="hidden_font_size" name="wc_3d_option['.$mat_obj_title.' Font Size]" value="Default" />';

					$html .= '</div>';

				}

				if(in_array('fontColor', $text_options)){

					$html .= '<div class="option_block_wrapper">';

					$obj_color_raw_options = array();

					if(isset( $mat_group['text_color_option' ] )) {
						$obj_text_raw_options = $mat_group['text_color_option' ];
					}

					$text_color_option = apply_filters( 'wc_3d_text_color_options', $obj_text_raw_options, $mat_group );

					$html .= '<h5 class="text_options_heading">Font Color:</h5>';

					if($text_color_option) {
						foreach ($text_color_option as $key => $mat_option) {

							$html .= '
							<div class="font_color" title="'.$mat_option["mat_title"].'" data-mat-title="'.$mat_option["mat_title"].'" data-text-id="'.$obj_mat_id.'" data-text-color="'.$mat_option["color"].'" style="background:'.$mat_option["color"].';"></div>';
						}

					}

					$html .= '<input type="hidden" class="hidden_font_color" name="wc_3d_option['.$mat_obj_title.' Font Color]" value="Default" />';

					$html .= '</div>';

				}

				if(in_array('font', $text_options)){

					$html .= '<div class="option_block_wrapper">';

					$html .= '<h5 class="text_options_heading">Font Options:</h5>';

					$rc_wc_3d_fonts = get_option( 'rc_wc_3d_fonts' );

					$fonts_data = $rc_wc_3d_fonts['rc_wc_fonts'];

					foreach ($fonts_data as $key => $value) {

						$html .= '<div class="fontFamily" data-font-url="'. $value['font_css'] .'" data-text-id="' . $obj_mat_id .'" data-font="'. $value['font_family'] .'" style="font-family: '. $value['font_family'] .'; font-size: 16px;">'. $value['font_id'] .'</div>';

					}

					$html .= '<input type="hidden" class="hidden_font_family" name="wc_3d_option['.$mat_obj_title.' Font Family]" value="Default" />';

					$html .= '</div>';

				}

				$html .= '</div>';

			}

			$html .= '</div>';
			$html .= '<input type="hidden" class="mat_value" name="wc_3d_option['.$mat_obj_title.']" value="" />';
			$html .= '<div class="clearfix"></div>';
			$html .= '</div>';

			echo $html;

		}

		//Frontend Gradient ConfigType Template
		function wc_3d_gradient_options_template($mat_group){

			$html = '<div class="material_group">';

			$mat_obj_title = $obj_mat_id = '';

			$obj_material_option = array();

			if ( isset( $mat_group['mat_obj_title' ] ) ) 
				$mat_obj_title = esc_html( $mat_group['mat_obj_title' ] );

			if ( isset( $mat_group['obj_mat_id' ] ) )
				$obj_mat_id = $mat_group['obj_mat_id' ];

			if ( !empty($mat_obj_title) ) {
				$html .= '<h3 class="wc_config_title">' .$mat_obj_title . '<span class="option_price"></span></h3>';
			}

			$html .= '<div class="material_options gradient_color">';

			$obj_gradient_raw_options = array();

			if(isset( $mat_group['obj_gradient_color' ] )) {
				$obj_gradient_raw_options = $mat_group['obj_gradient_color' ];
			}

			$obj_gradient_color = apply_filters( 'wc_3d_gradient_options', $obj_gradient_raw_options, $mat_group );

			if($obj_gradient_color) {

				if(isset($mat_group['gradient_options'])){
					$gradient_options = $mat_group['gradient_options'];
				}

				if(in_array('color1', $gradient_options)){

					$html .= '<div class="option_block_wrapper">';

					$html .= '<h5 class="text_options_heading">Color Options:</h5>';

					foreach ($obj_gradient_color as $key => $mat_option) {

						$html .= '
						<div class="gradient_option gcolor1" title="'.$mat_option["mat_title"].'" data-mat-title="'.$mat_option["mat_title"].'" data-mat="'.$obj_mat_id.'" data-mat-color="'.$mat_option["color"].'" style="background: '.$mat_option["color"].';"></div>
						';
					}

					$html .= '</div>';

				}

				if(in_array('color2', $gradient_options)){

					$html .= '<div class="option_block_wrapper">';

					$html .= '<h5 class="text_options_heading">Color Options:</h5>';

					foreach ($obj_gradient_color as $key => $mat_option) {

						$html .= '
						<div class="gradient_option gcolor2" title="'.$mat_option["mat_title"].'" data-mat-title="'.$mat_option["mat_title"].'" data-mat="'.$obj_mat_id.'" data-mat-color="'.$mat_option["color"].'" style="background: '.$mat_option["color"].';"></div>
						';
					}

					$html .= '</div>';
				}

			}

			$html .= '</div>';
			$html .= '<input type="hidden" class="mat_value" name="wc_3d_option['.$mat_obj_title.']" value="" />';
			$html .= '<div class="clearfix"></div>';
			$html .= '</div>';

			echo $html;

		}

		//Frontend Img ConfigType Template
		function wc_3d_img_options_template($mat_group){

			$html = '<div class="material_group">';

			$mat_obj_title = $obj_mat_id = '';

			$obj_material_option = array();

			if ( isset( $mat_group['mat_obj_title' ] ) ) 
				$mat_obj_title = esc_html( $mat_group['mat_obj_title' ] );

			if ( isset( $mat_group['obj_mat_id' ] ) )
				$obj_mat_id = $mat_group['obj_mat_id' ];

			if ( !empty($mat_obj_title) ) {
				$html .= '<h3 class="wc_config_title">' .$mat_obj_title . '<span class="option_price"></span></h3>';
			}

			$html .= '<div class="material_options img_color">';

			$obj_img_raw_options = array();

			if(isset( $mat_group['obj_image_option' ] )) {
				$obj_img_raw_options = $mat_group['obj_image_option' ];
			}

			$obj_image_option = apply_filters( 'wc_3d_img_options', $obj_img_raw_options, $mat_group );

			if($obj_image_option) {
				
				foreach ($obj_image_option as $key => $mat_option) {

					$mat_map = "";
					if(isset($mat_option["map"])){
						$mat_map = $mat_option["map"];
					}
					if(isset($mat_option["mat_price"])){
						$mat_price = $mat_option["mat_price"];
					}
					$html .= '
					<div class="img_option" title="'.$mat_option["mat_title"].'" data-mat-title="'.$mat_option["mat_title"].'" data-mat="'.$obj_mat_id.'" data-mat-map="'.$mat_map.'" style="background: url('.$mat_option["map"].') center no-repeat; background-size: cover;"></div>
					';
				}

			}

			$user_upload = "off";

			if ( isset( $mat_group['user_upload' ] ) )
				$user_upload = $mat_group['user_upload' ];

			if($user_upload == 'on') {


				$html .= '
				<div class="dropzone" data-mat="'.$obj_mat_id.'">
				<div class="fallback">
				<input name="file" type="file" multiple />
				</div>
				</div>
				';

			}

			$html .= '</div>';
			$html .= '<input type="hidden" class="mat_value" name="wc_3d_option['.$mat_obj_title.']" value="" />';
			$html .= '<div class="clearfix"></div>';
			$html .= '</div>';

			echo $html;

		}

		//Frontend Pattern ConfigType Template
		function wc_3d_pattern_options_template($mat_group){

			$html = '<div class="material_group">';

			$mat_obj_title = $obj_mat_id = '';

			$obj_material_option = array();

			if ( isset( $mat_group['mat_obj_title' ] ) ) 
				$mat_obj_title = esc_html( $mat_group['mat_obj_title' ] );

			if ( isset( $mat_group['obj_mat_id' ] ) )
				$obj_mat_id = $mat_group['obj_mat_id' ];

			if ( !empty($mat_obj_title) ) {
				$html .= '<h3 class="wc_config_title">' .$mat_obj_title . '<span class="option_price"></span></h3>';
			}

			$html .= '<div class="material_options pattern_color">';

			$obj_pattern_raw_options = array();

			if(isset( $mat_group['obj_pattern_option' ] )) {
				$obj_pattern_raw_options = $mat_group['obj_pattern_option' ];
			}

			$obj_pattern_option = apply_filters( 'wc_3d_pattern_options', $obj_pattern_raw_options, $mat_group );


			if($obj_pattern_option) {
				foreach ($obj_pattern_option as $key => $mat_option) {

					$mat_map = "";
					
					if(isset($mat_option["map"])){
						$mat_map = $mat_option["map"];
					}

					$html .= '
					<div class="pattern_option" title="'.$mat_option["mat_title"].'" data-mat-title="'.$mat_option["mat_title"].'" data-mat="'.$obj_mat_id.'" data-pattern-id="'.$mat_option["pattern_id"].'" data-mat-map="'.$mat_map.'" style="background: url('.$mat_option["map"].') center no-repeat; background-size: cover;"></div>
					';
				}
			}

			$html .= '</div>';
			$html .= '<input type="hidden" class="mat_value" name="wc_3d_option['.$mat_obj_title.']" value="" />';
			$html .= '<div class="clearfix"></div>';
			$html .= '</div>';

			echo $html;

		}

		//Frontend Object ConfigType Template
		function wc_3d_object_options_template($mat_group){

			$html = '<div class="material_group">';

			$mat_obj_title = $obj_mat_id = '';

			$obj_material_option = array();

			if ( isset( $mat_group['mat_obj_title' ] ) ) 
				$mat_obj_title = esc_html( $mat_group['mat_obj_title' ] );

			if ( isset( $mat_group['obj_mat_id' ] ) )
				$obj_mat_id = $mat_group['obj_mat_id' ];

			if ( !empty($mat_obj_title) ) {
				$html .= '<h3 class="wc_config_title">' .$mat_obj_title . '<span class="option_price"></span></h3>';
			}

			$html .= '<div class="material_options object_color">';

			$html .= '<div class="object_initial_data" data-show="'.$obj_mat_id.'" data-hide="'.$mat_group['obj_hide_name' ].'"></div>';

			$obj_show_raw_options = array();

			if(isset( $mat_group['obj_show_options' ] )) {
				$obj_show_raw_options = $mat_group['obj_show_options' ];
			}

			$obj_show_options = apply_filters( 'wc_3d_obj_show_options', $obj_show_raw_options, $mat_group );

			if($obj_show_options) {
				
				foreach ($obj_show_options as $key => $mat_option) {

					$mat_map = "";

					if(isset($mat_option["map"])){
						$mat_map = $mat_option["map"];
					}

					$html .= '
					<div class="obj_option" title="'.$mat_option["mat_title"].'" data-mat-title="'.$mat_option["mat_title"].'" data-obj-name="'.$mat_option["obj_name"].'" data-mat-map="'.$mat_map.'" style="background: url('.$mat_option["map"].') center no-repeat; background-size: cover;"></div>
					';
				}
			}
			

			$html .= '</div>';
			$html .= '<input type="hidden" class="mat_value" name="wc_3d_option['.$mat_obj_title.']" value="" />';
			$html .= '<div class="clearfix"></div>';
			$html .= '</div>';

			echo $html;

		}

		//Frontend Material ConfigType Template
		function wc_3d_material_options_template($mat_group){

			$html = '<div class="material_group">';

			$mat_obj_title = $obj_mat_id = $default = '';

			$obj_material_option = array();

			if ( isset( $mat_group['mat_obj_title' ] ) ) 
				$mat_obj_title = esc_html( $mat_group['mat_obj_title' ] );

			if ( isset( $mat_group['obj_mat_id' ] ) )
				$obj_mat_id = $mat_group['obj_mat_id' ];

			if ( !empty($mat_obj_title) ) {
				$html .= '<h3 class="wc_config_title">' .$mat_obj_title . '<span class="option_price"></span></h3>';
			}

			$html .= '<div class="material_options mat_color">';

			$obj_material_raw_options = array();

			if(isset( $mat_group['obj_material_option' ] )) {
				$obj_material_raw_options = $mat_group['obj_material_option' ];
			}

			$obj_material_option = apply_filters( 'wc_3d_materials_options', $obj_material_raw_options, $mat_group );

			if($obj_material_option) {

				foreach ($obj_material_option as $key => $mat_option) {

					$css_style = $mat_map = $mat_price = $mat_color = $is_default = $class = "";

					if(isset($mat_option["color"])){
						$mat_color = $mat_option["color"];
					}

					if(isset($mat_option["default"])){
						$is_default = $mat_option["default"];
						if($is_default == 'on'){
							$class = "active";
							$default = $mat_option["mat_title"];
						}
					}

					$css_style = 'background: '.$mat_color;

					if(isset($mat_option["map"])){
						$mat_map = $mat_option["map"];
						$mat_image_id = attachment_url_to_postid($mat_map);
						$mat_thumb_image_array = wp_get_attachment_image_src($mat_image_id, 'thumbnail', true);
						$mat_thumb_image = $mat_thumb_image_array[0];

						$css_style = 'background: url(' . $mat_thumb_image . ') no-repeat center; background-size: cover;';
					}
					if(isset($mat_option["mat_price"])){
						$mat_price = $mat_option["mat_price"];
					}

					$html .= '
					<div class="mat_option ' .$class.'" title="'.$mat_option["mat_title"].'" data-mat-title="'.$mat_option["mat_title"].'" data-mat-price="'.$mat_price.'" data-mat="'.$obj_mat_id.'" data-mat-color="'.$mat_color.'" data-mat-map="'.$mat_map.'" style="'.$css_style.'"></div>
					';
				}

			}

			$html .= '</div>';

			$html .= '<input type="hidden" class="mat_value" name="wc_3d_option['.$mat_obj_title.']" value="'.$default.'" />';

			$rc_wc_options = get_option( 'rc_wc_3d_products' );
			$use_price_option = $rc_wc_options['rc_wc_use_price_option'];

			if($use_price_option == 'yes'){
				$html .= '<input type="hidden" class="wc_mat_price" name="wc_3d_option['.$mat_obj_title.' Price]" value="0" />';
			}

			$html .= '<div class="clearfix"></div>';
			$html .= '</div>';

			echo $html;

		}

		//Frontend Choices ConfigType Template
		function wc_3d_choices_options_template($mat_group){

			$html = '<div class="material_group">';

			$mat_obj_title = $obj_mat_id = '';

			$obj_material_option = array();

			if ( isset( $mat_group['mat_obj_title' ] ) ) 
				$mat_obj_title = esc_html( $mat_group['mat_obj_title' ] );

			if ( isset( $mat_group['obj_mat_id' ] ) )
				$obj_mat_id = $mat_group['obj_mat_id' ];

			if ( !empty($mat_obj_title) ) {
				$html .= '<h3 class="wc_config_title">' .$mat_obj_title . '<span class="option_price"></span></h3>';
			}

			$html .= '<div class="material_options object_color">';

			$obj_show_raw_options = array();

			if(isset( $mat_group['obj_choices_options' ] )) {
				$obj_show_raw_options = $mat_group['obj_choices_options' ];
			}

			$obj_show_options = apply_filters( 'wc_3d_obj_show_options', $obj_show_raw_options, $mat_group );

			if($obj_show_options) {
				
				foreach ($obj_show_options as $key => $mat_option) {
					$mat_map = "";

					if(isset($mat_option["map"])){
						$mat_map = $mat_option["map"];
					}
					$html .= '<div style="width:50px;">';
					$html .= '
					<div class="obj_option" title="'.$mat_option["mat_title"].'" data-mat-title="'.$mat_option["mat_title"].'" data-obj-name="'.$mat_option["obj_name"].'" data-mat-map="'.$mat_map.'" style="background: url('.$mat_option["map"].') center no-repeat; background-size: cover;"></div>
					';
					$html .= '<div style="text-align:center;">';
					$html .= $mat_option['mat_title'];
					$html .= '</div>';
					$html .= '</div>';
				}
			}
			

			$html .= '</div>';
			$html .= '<input type="hidden" class="mat_value" name="wc_3d_option['.$mat_obj_title.']" value="" />';
			$html .= '<div class="clearfix"></div>';
			$html .= '</div>';

			echo $html;

		}

		//Frontend Fabric Gallery ConfigType Template
		function wc_3d_fabricgallery_options_template($mat_group){
			$html = '<div class="material_group">';

			$mat_obj_title = $obj_mat_id = '';

			$obj_material_option = array();

			if ( isset( $mat_group['mat_obj_title' ] ) ) 
				$mat_obj_title = esc_html( $mat_group['mat_obj_title' ] );

			if ( isset( $mat_group['obj_mat_id' ] ) )
				$obj_mat_id = $mat_group['obj_mat_id' ];

			if ( !empty($mat_obj_title) ) {
				$html .= '<h3 class="wc_config_title">' .$mat_obj_title . '<span class="option_price"></span></h3>';
			}

			$html .= '<div class="material_options object_color">';

			$obj_show_raw_options = array();

			if(isset( $mat_group['obj_fabricgallery_options' ] )) {
				$obj_show_raw_options = $mat_group['obj_fabricgallery_options' ];
			}

			$obj_show_options = apply_filters( 'wc_3d_obj_show_options', $obj_show_raw_options, $mat_group );

			if($obj_show_options) {
				$html .= "<div class='fabric-selector flex-container mt-1'>";
				foreach ($obj_show_options as $key => $mat_option) {
					$count = substr(md5(mt_rand()), 0, 7);
					$mat_map = "";

					if(isset($mat_option["map"])){
						$mat_map = $mat_option["map"];
					}
					$html .= "<div class='fabric'>";
						$html .= "<div class='title'>";
							$html .= $mat_option['mat_title'];
						$html .= "</div>";
						$html .= "<div id='".$count."-fabric-container' class='selector-preview'>";

						$html .= "</div>";
						$html .= "<div class='change-fabric'>";
							$html .= "<button type='button' data-mat-title='".$mat_option['mat_title']."'  data-mat='".$mat_option['obj_name']."' data-container='".$count."-fabric-container' class='btn' qr-fabric-picker='true' qr-fabric-picker-id='qr-fabric-picker' qr-fabric-container='fabric-container-a'>";
								$html .= "Change Fabric";
							$html .= "</button>";
						$html .= "</div>";
					$html .= "</div>";
				}
				
				$html .= "</div>";
			}
			

			$html .= '</div>';
			$html .= '<input type="hidden" class="mat_value" name="wc_3d_option['.$mat_obj_title.']" value="" />';
			$html .= '<div class="clearfix"></div>';
			$html .= '</div>';

			echo $html;

		}

		public function wc_3d_frontend_options( $post_id = false, $prefix = false ) {
			
			global $product, $post;

			//Post Meta Prefix
			$prefix = 'wc_3d_';
			//Getting Post Id
			$post_id = $post->ID;

			$wc_3d_activate = get_post_meta($post_id , $prefix . 'activate', true);

			//Checking if WC 3D is activated on product or not.
			if($wc_3d_activate != 'on')
				return;

			$rc_wc_options = get_option( 'rc_wc_3d_products' );
			$use_price_option = $rc_wc_options['rc_wc_use_price_option'];

			echo '<div class="wc_3d_materials">';

			do_action('wc_3d_before_material_group');

			$mat_groups = get_post_meta($post_id , $prefix . 'material_group', true);

			foreach((array)$mat_groups as $key => $mat_group) {

				if ( isset( $mat_group['config_type' ] ) ) {
					$config_type = esc_html( $mat_group['config_type' ] );
				} else {
					$config_type = "mat";
				}

				if($config_type == 'text') {

					do_action('wc_3d_text_template', $mat_group);

				} else if( $config_type == 'gradient') {

					do_action('wc_3d_gradient_template', $mat_group);

				} else if( $config_type == 'img') {

					do_action('wc_3d_img_template', $mat_group);
					
				} else if( $config_type == 'pattern') {

					do_action('wc_3d_pattern_template', $mat_group);
					
				} else if( $config_type == 'object') {

					do_action('wc_3d_object_template', $mat_group);
					
				} else if($config_type == 'choices'){
					do_action('wc_3d_choices_template', $mat_group);
				} else if($config_type == 'fabricgallery'){
					do_action('wc_3d_fabricgallery_template', $mat_group);
				} else {

					do_action('wc_3d_material_template', $mat_group);
				}
				
			}

			do_action('wc_3d_extra_process');

			if($use_price_option == 'yes')
				echo '<input type="hidden" id="finalPrice" value="'.$product->get_price().'" name="finalPrice" />';
			
			echo '<input type="hidden" id="productId" value="'.$post_id.'" name="productId" />';
			echo '<input type="hidden" id="wc3dimgurl" value="" name="wc3dimageurl" />';
			echo '<input type="hidden" id="wc3dsvgurl" value="" name="wc3dsvgurl" />';
			if($use_price_option == 'yes'){
				echo '<div class="sub_total"></div>';
				echo '<div class="total"></div>';
			}
			echo '</div>';
			include RC_3D_INC.'/templates/fabric-picker.php';
			
		}

	}

	new RC_3D_Template;

}