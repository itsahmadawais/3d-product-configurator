<?php
/**
 * Ajax class
 *
 * @author Rcreators Websolutions
 * @package Woocommerce 3D products
 * @version 1.0.0
 */

if ( !defined( 'RC_3D' ) ) {
    exit;
} // Exit if accessed directly


if ( !class_exists( 'RC_3D_Ajax' ) ) {

	/**
     * WooCommerce 3D Products
     *
     * @since 1.0.0
     */
    class RC_3D_Ajax extends RC_3D {

    	public function __construct() {
        	
        	//Get AJax 3D data
            add_action( 'wp_ajax_wc_rc_getproductdata', array( $this, 'wc_rc_getproductdata') );
            add_action( 'wp_ajax_nopriv_wc_rc_getproductdata', array( $this, 'wc_rc_getproductdata') );

            //Upload AJax
            add_action( 'wp_ajax_handle_dropped_media', array( $this, 'handle_dropped_media') );
            add_action( 'wp_ajax_nopriv_handle_dropped_media', array( $this, 'handle_dropped_media') );

            //Remove File
            add_action( 'wp_ajax_handle_deleted_media', array( $this, 'handle_deleted_media') );
            add_action( 'wp_ajax_nopriv_handle_deleted_media', array( $this, 'handle_deleted_media') );

            //Save Product Image for cart
            add_action( 'wp_ajax_wc_3d_product_image', array( $this, 'wc_3d_product_image') );
            add_action( 'wp_ajax_nopriv_wc_3d_product_image', array( $this, 'wc_3d_product_image') );

            //Save Product Image for cart
            add_action( 'wp_ajax_wc_3d_product_svg', array( $this, 'wc_3d_product_svg') );
            add_action( 'wp_ajax_nopriv_wc_3d_product_svg', array( $this, 'wc_3d_product_svg') );

        }

        function wc_rc_getproductdata() {

            $prefix = 'wc_3d_';

            $post_id = $_REQUEST['productID'];

            $wc_3d_product_data = array();

            $obj_file = get_post_meta($post_id , $prefix . 'obj_file', true);

            if($obj_file){

                $wc_3d_debug_activate = get_post_meta($post_id , $prefix . 'debug_activate', true);
                //Checking Which Object Type is loading and enqueue script based on it.
                $wc_3d_object_type = get_post_meta($post_id , $prefix . '3d_object_type', true);
                //Object Parameters

                $obj_position = get_post_meta($post_id , $prefix . 'obj_position', true);
                //Camera Parameters
                $camera_position = get_post_meta($post_id , $prefix . 'camera_position', true);
                $camera_rotation = get_post_meta($post_id , $prefix . 'camera_rotation', true);
                $camera_zoom_min = get_post_meta($post_id , $prefix . 'camera_zoom_min', true);
                $camera_zoom_max = get_post_meta($post_id , $prefix . 'camera_zoom_max', true);
                //Canvas Parameters
                $canvas_bg = get_post_meta($post_id , $prefix . 'canvas_bg', true);
                $canvas_transparant = get_post_meta($post_id , $prefix . 'canvas_transparant', true);

                $env_map = get_post_meta($post_id , $prefix . 'env_map', true);
                $env_bg = get_post_meta($post_id , $prefix . 'env_bg', true);
                //Light Paramerters
                $disable_lights = get_post_meta($post_id , $prefix . 'disable_lights', true);
                $lights = get_post_meta($post_id , $prefix . 'lights', true);

                $wc_3d_svg_active = get_post_meta($post_id , $prefix . 'svg_activate', true);
                $wc_3d_svg_file = get_post_meta($post_id , $prefix . 'svg_file', true);

                $wc_3d_product_data['wc_3d_debug_activate'] = $wc_3d_debug_activate;
                $wc_3d_product_data['wc_3d_object_type'] = $wc_3d_object_type;
                $wc_3d_product_data['obj_file'] = $obj_file;
                $wc_3d_product_data['obj_position'] = $obj_position;
                $wc_3d_product_data['camera_position'] = $camera_position;
                $wc_3d_product_data['camera_rotation'] = $camera_rotation;
                $wc_3d_product_data['camera_zoom_min'] = $camera_zoom_min;
                $wc_3d_product_data['camera_zoom_max'] = $camera_zoom_max;
                $wc_3d_product_data['canvas_bg'] = $canvas_bg;
                $wc_3d_product_data['canvas_transparant'] = $canvas_transparant;
                $wc_3d_product_data['env_map'] = $env_map;
                $wc_3d_product_data['env_bg'] = $env_bg;
                $wc_3d_product_data['disable_lights'] = $disable_lights;
                $wc_3d_product_data['lights'] = $lights;
                $wc_3d_product_data['svg_active'] = $wc_3d_svg_active;

            }

            echo json_encode($wc_3d_product_data);
            die();

        }

        function handle_dropped_media() {
            status_header(200);

            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['path'] . DIRECTORY_SEPARATOR;
            $num_files = count($_FILES['file']['tmp_name']);

            $newupload = 0;

            if ( !empty($_FILES) ) {
                $files = $_FILES;
                foreach($files as $file) {
                    $newfile = array (
                            'name' => $file['name'],
                            'type' => $file['type'],
                            'tmp_name' => $file['tmp_name'],
                            'error' => $file['error'],
                            'size' => $file['size']
                    );

                    $_FILES = array('upload'=>$newfile);
                    foreach($_FILES as $file => $array) {
                        $newupload = media_handle_upload( $file, 0 );
                    }
                }
            }

            echo $newupload;    
            die();
        }

        function handle_deleted_media(){

            if( isset($_REQUEST['media_id']) ){
                $post_id = absint( $_REQUEST['media_id'] );

                $status = wp_delete_attachment($post_id, true);

                if( $status )
                    echo json_encode(array('status' => 'OK'));
                else
                    echo json_encode(array('status' => 'FAILED'));
            }

            die();
        }

		function wc_3d_product_image() {

            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['path'] . DIRECTORY_SEPARATOR;

            $img = $_REQUEST['imgBase64'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);

            $decoded = base64_decode($img);

            $filename = 'woocommerce_3d_products.png';

            $hashed_filename = md5( $filename . microtime() ) . '_' . $filename;

            // @new
            $image_upload = file_put_contents( $upload_path . $hashed_filename, $decoded );

            //HANDLE UPLOADED FILE
            if( !function_exists( 'wp_handle_sideload' ) ) {
              require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }

            // Without that I'm getting a debug error!?
            if( !function_exists( 'wp_get_current_user' ) ) {
              require_once( ABSPATH . 'wp-includes/pluggable.php' );
            }

            // @new
            $file = array();
            $file['error'] = '';
            $file['tmp_name'] = $upload_path . $hashed_filename;
            $file['name'] = $hashed_filename;
            $file['type'] = 'image/png';
            $file['size'] = filesize( $upload_path . $hashed_filename );

            // upload file to server
            // @new use $file instead of $image_upload
            $file_return = wp_handle_sideload( $file, array( 'test_form' => false ) );

            echo json_encode($file_return);

            die();

        }

        function wc_3d_product_svg() {

            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['path'] . DIRECTORY_SEPARATOR;

            $svgData = stripslashes($_REQUEST['svgData']);

            $filename = 'woocommerce_3d_products.svg';
            $hashed_filename = md5( $filename . microtime() ) . '_' . $filename;

            $svg_upload = file_put_contents( $upload_path . $hashed_filename, $svgData );

            //HANDLE UPLOADED FILE
            if( !function_exists( 'wp_handle_sideload' ) ) {
              require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }

            // Without that I'm getting a debug error!?
            if( !function_exists( 'wp_get_current_user' ) ) {
              require_once( ABSPATH . 'wp-includes/pluggable.php' );
            }

            // @new
            $file = array();
            $file['error'] = '';
            $file['tmp_name'] = $upload_path . $hashed_filename;
            $file['name'] = $hashed_filename;
            $file['type'] = 'image/svg+xml';
            $file['size'] = filesize( $upload_path . $hashed_filename );

            // upload file to server
            // @new use $file instead of $image_upload
            $file_return = wp_handle_sideload( $file, array( 'test_form' => false ) );

            echo json_encode($file_return);

            die();
        }

    }

    new RC_3D_Ajax;

}