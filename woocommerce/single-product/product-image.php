<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product, $post_id;

if ( ! $post_id ) {
	global $post;
	$post_id = $post->ID;
}

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $product->get_image_id() ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);

$prefix = 'wc_3d_';
$obj_file = get_post_meta($post_id , $prefix . 'obj_file', true);

$wc_3d_svg_active = get_post_meta($post_id , $prefix . 'svg_activate', true);
$wc_3d_svg_file = get_post_meta($post_id , $prefix . 'svg_file', true);

//Load WC 3D if Obj file Selected
?>
<div id="wc_3d_main" class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<div class="wc-3d-product-wrapper" id="wc-3d-wrapper">
		<div id="loaderhtml" class="loaderhtml">
			<div id="progressbar" class="progressbar w3-light-grey">
				<div id="progress" class="progress">
				</div>
			</div>
		</div>
		<canvas class="wc_3d_product_canvas" id="wc_3d">
		</canvas>
		<div class="wc_3d_player">
			<div class="fa fa-sync-alt play_pause_toggle" title="Auto Rotate"></div>
			<div class="fa fa-save save_image" title="Save Image"></div>
			<div class="fa fa-compress fullscreen_toggle" title="Fullscreen"></div>
		</div>
		<?php if($wc_3d_svg_active == 'on') { 
			$wc_svg_material = '';
			$wc_3d_svg_material_enable = get_post_meta($post_id , $prefix . 'svg_material_enable', true);
			if($wc_3d_svg_material_enable == 'on'){
				$wc_svg_material = get_post_meta($post_id , $prefix . 'svg_material', true);
			}
			?>
			<div id="svg_data" data-svgM="<?php echo $wc_svg_material; ?>" style="display: none;">
				<?php echo file_get_contents($wc_3d_svg_file); ?>
			</div>
		<?php } ?>
	</div>
</div>