<?php
/**
 * Admin init class
 *
 * @author  Rcreators Websolutions
 * @package Woocommerce 3D Products
 * @version 1.0.0
 */

if ( ! defined( 'RC_3D' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'RC_3D_Admin_Init' ) ) {
	/**
	 * Initiator class. Create and populate admin views.
	 *
	 * @since 1.0.0
	 */
	 
	
	class RC_3D_Admin_Init {
		
		/**
         * Single instance of the class
         */
        protected static $instance;
		
		/**
         * Returns single instance of the class
         *
         * @return \RC_3D_Admin_Init
         * @since 2.0.0
         */
        public static function get_instance(){
            if( is_null( self::$instance ) ){
                self::$instance = new self( $_REQUEST );
            }

            return self::$instance;
        }
		
		public function __construct(){

			
        }
		

	}
}

/**
 * Unique access to instance of RC_3D_Admin_Init class
 * @since 1.0.0
 */
function RC_3D_Admin_Init(){
	return RC_3D_Admin_Init::get_instance();
}