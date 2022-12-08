<?php

/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
function rc_wc_3d_register_main_options_metabox() {

	/**
	 * Registers main options page menu item and form.
	 */
	$main_options = new_cmb2_box( array(
		'id'           => 'rc_wc_3d_products',
		'title'        => esc_html__( 'WooCommerce 3D Product Options', TEXTDOMAIN ),
		'object_types' => array( 'options-page' ),
		'option_key'      => 'rc_wc_3d_products', // The option key and admin menu page slug.
		'menu_title'      => esc_html__( 'WC 3D Options', TEXTDOMAIN ), // Falls back to 'title' (above).
		//'parent_slug'     => 'options-general.php', // Make options page a submenu item of the themes menu.
		'tab_group'    => 'rc_wc_3d_products',
		'tab_title'    => 'General Options',
	) );

	$main_options->add_field( array(
		'name' => 'Price Option',
		'type' => 'title',
		'id'   => 'rc_wc_3d_price_title'
	) );

	$main_options->add_field( array(
		'name'    => 'Use Custom Price Option',
		'id'      => 'rc_wc_use_price_option',
		'type'    => 'radio_inline',
		'options' => array(
			'yes' => __( 'Yes', TEXTDOMAIN ),
			'no'   => __( 'No', TEXTDOMAIN ),
		),
	) );

	$main_options->add_field( array(
		'name' => 'Shadow Option',
		'type' => 'title',
		'id'   => 'rc_wc_3d_shadow_title'
	) );

	$main_options->add_field( array(
		'name'    => 'Cast Shadow',
		'id'      => 'rc_wc_cast_shadow',
		'type'    => 'radio_inline',
		'options' => array(
			'yes' => __( 'Yes', TEXTDOMAIN ),
			'no'   => __( 'No', TEXTDOMAIN ),
		),
	) );

	$main_options->add_field( array(
		'name' => 'Camera Control',
		'type' => 'title',
		'id'   => 'rc_wc_3d_camera_control_title'
	) );

	$main_options->add_field( array(
		'name'    => 'Allow to look downside',
		'id'      => 'rc_wc_allow_downside',
		'type'    => 'radio_inline',
		'options' => array(
			'yes' => __( 'Yes', TEXTDOMAIN ),
			'no'   => __( 'No', TEXTDOMAIN ),
		),
	) );


	/* Fonts Library */
	$fonts_options = new_cmb2_box( array(
		'id'           => 'rc_wc_3d_fonts_library',
		'title'        => esc_html__( 'WooCommerce 3D Product Font Library', TEXTDOMAIN ),
		'object_types' => array( 'options-page' ),
		'option_key'      => 'rc_wc_3d_fonts',
		'parent_slug'     => 'rc_wc_3d_products',
		'tab_group'    => 'rc_wc_3d_products',
		'tab_title'    => 'Font Library',
		'menu_title'      => esc_html__( 'Font Library', TEXTDOMAIN ),
	) );

	$fonts_options->add_field( array(
		'name' => 'Add your fonts',
		'type' => 'title',
		'id'   => 'rc_wc_3d_font_title'
	) );

	$font_family = $fonts_options->add_field( array(
		'id'          => 'rc_wc_fonts',
		'type'        => 'group',
		'options'     => array(
			'group_title'       => __( 'Entry {#}', TEXTDOMAIN ),
			'add_button'        => __( 'Add Font Row', TEXTDOMAIN ),
			'remove_button'     => __( 'Remove Font', TEXTDOMAIN ),
			'sortable'          => true,
			'closed'         => true,
		),
	) );

	$fonts_options->add_group_field( $font_family, array(
		'name' => __('Font Id', TEXTDOMAIN ),
		'id'   => 'font_id',
		'type' => 'text',
	) );

	$fonts_options->add_group_field( $font_family, array(
		'name' => __('Font Family Name', TEXTDOMAIN ),
		'id'   => 'font_family',
		'type' => 'text',
	) );

	$fonts_options->add_group_field( $font_family, array(
		'name' => __( 'Font CSS URL', TEXTDOMAIN ),
		'id'   => 'font_css',
		'type' => 'file',
	) );

	/* Design Options */
	$design_options = new_cmb2_box( array(
		'id'           => 'rc_wc_3d_design_option',
		'title'        => esc_html__( 'WooCommerce 3D Product Design Options', TEXTDOMAIN ),
		'object_types' => array( 'options-page' ),
		'option_key'      => 'rc_wc_3d_design', // The option key and admin menu page slug.
		'parent_slug'     => 'rc_wc_3d_products', // Make options page a submenu item of the themes menu.
		'tab_group'    => 'rc_wc_3d_products',
		'tab_title'    => 'Design Options',
		'menu_title'      => esc_html__( 'Design Options', TEXTDOMAIN ), // Falls back to 'title' (above).
	) );

	$design_options->add_field( array(
		'name' => 'Design Options',
		'type' => 'title',
		'id'   => 'rc_wc_3d_design_title'
	) );

	$design_options->add_field( array(
		'name' => __('Accordian Heading Background Color', TEXTDOMAIN ),
		'id'   => 'mat_title_bg',
		'type' => 'colorpicker',
	) );

	$design_options->add_field( array(
		'name' => __('Accordian Heading Text Color', TEXTDOMAIN ),
		'id'   => 'mat_title_text',
		'type' => 'colorpicker',
	) );

}
add_action( 'cmb2_admin_init', 'rc_wc_3d_register_main_options_metabox' );