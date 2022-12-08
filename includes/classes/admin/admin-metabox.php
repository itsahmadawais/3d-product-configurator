<?php
if ( file_exists( RC_3D_INC . 'library/cmb2/init.php' ) ) {
	require_once RC_3D_INC . 'library/cmb2/init.php';
} elseif ( file_exists( RC_3D_INC . 'library/CMB2/init.php' ) ) {
	require_once RC_3D_INC . 'library/CMB2/init.php';
}

if ( file_exists( RC_3D_INC . 'library/cmb2/plugins/cmb2-conditionals.php' ) ) {
	require_once RC_3D_INC . 'library/cmb2/plugins/cmb2-conditionals.php';
	cmb2_conditionals_init();
}
/**
 * Including the files and the classes for the custom fields.
 * Initiallizing the objects.
 * Adding fields for the admin.
 */

//Matieral Field
function cmb2_init_material_field() {
	require_once RC_3D_INC . 'library/cmb2/plugins/class-cmb2-render-material-field.php';
	CMB2_Render_Material_Field::init();
}
add_action( 'cmb2_init', 'cmb2_init_material_field' );

//Gradient Field
function cmb2_init_gradient_field() {
	require_once RC_3D_INC . 'library/cmb2/plugins/class-cmb2-render-gradient-field.php';
	CMB2_Render_Gradient_Field::init();
}
add_action( 'cmb2_init', 'cmb2_init_gradient_field' );

//Image Field
function cmb2_init_image_field() {
	require_once RC_3D_INC . 'library/cmb2/plugins/class-cmb2-render-image-field.php';
	CMB2_Render_Image_Field::init();
}
add_action( 'cmb2_init', 'cmb2_init_image_field' );

//Pattern Field
function cmb2_init_pattern_field() {
	require_once RC_3D_INC . 'library/cmb2/plugins/class-cmb2-render-pattern-field.php';
	CMB2_Render_Pattern_Field::init();
}
add_action( 'cmb2_init', 'cmb2_init_pattern_field' );

//Object Field
function cmb2_init_object_field() {
	require_once RC_3D_INC . 'library/cmb2/plugins/class-cmb2-render-object-field.php';
	CMB2_Render_Object_Field::init();
}
add_action( 'cmb2_init', 'cmb2_init_object_field' );

//Choices Field
function cmb2_init_choices_field() {
	require_once RC_3D_INC . 'library/cmb2/plugins/class-cmb2-render-choices-field.php';
	CMB2_Render_Choices_Field::init();
}
add_action( 'cmb2_init', 'cmb2_init_choices_field' );

//FabricGallery Field
function cmb2_init_fabricgallery_field() {
	require_once RC_3D_INC . 'library/cmb2/plugins/class-cmb2-render-fabricgallery-field.php';
	CMB2_Render_FabricGallery_Field::init();
}
add_action( 'cmb2_init', 'cmb2_init_fabricgallery_field' );


/**
 * Registering CMB2 Metaboxes Filed for the Admin
 */
add_action( 'cmb2_admin_init', 'wc_3d_register_metabox' );

function wc_3d_register_metabox() {
	
	$prefix = 'wc_3d_';

	$wc_3d_fields = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Product 3D Data', TEXTDOMAIN ),
		'object_types'  => array( 'product' ), // Post type
		'context'    => 'normal',
		'priority'   => 'default',
		'show_names' => true, // Show field names on the left
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Activate 3D Product', TEXTDOMAIN ),
		'desc' => esc_html__( 'Enable 3D product options', TEXTDOMAIN ),
		'id'   => $prefix . 'activate',
		'type' => 'checkbox',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Activate Debug', TEXTDOMAIN ),
		'desc' => esc_html__( 'Enable debug mode', TEXTDOMAIN ),
		'id'   => $prefix . 'debug_activate',
		'type' => 'checkbox',
	) );

	$file_types = array(
		'dae' => __( 'Collada File', TEXTDOMAIN ),
		'fbx'   => __( 'FBX File', TEXTDOMAIN ),
		'json'   => __( 'Json File', TEXTDOMAIN ),
		'gltf'   => __( 'GLTF File', TEXTDOMAIN ),
	);

	$file_types = apply_filters('rc_3d_file_types', $file_types );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Type of 3D object file', TEXTDOMAIN ),
		'desc' => esc_html__( 'what extension of your 3d file ?', TEXTDOMAIN ),
		'id'   => $prefix . '3d_object_type',
		'type' => 'select',
		'default'          => 'fbx',
		'options'          => $file_types,
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Select 3D model file', TEXTDOMAIN ),
		'desc' => esc_html__( 'Upload an obj or Fbx file of your model based on your model type selection above.', TEXTDOMAIN ),
		'id'   => $prefix . 'obj_file',
		'type' => 'file',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Object Position', TEXTDOMAIN ),
		'desc' => esc_html__( 'Set Position of object in scene ( x, y, z )', TEXTDOMAIN ),
		'id'   => $prefix . 'obj_position',
		'type' => 'text',
		'default' => '0, 0, 0',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Canvas Background', TEXTDOMAIN ),
		'desc' => esc_html__( 'Canvas Background color', TEXTDOMAIN ),
		'id'   => $prefix . 'canvas_bg',
		'type' => 'colorpicker',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Background Transprant', TEXTDOMAIN ),
		'desc' => esc_html__( 'Remove Background', TEXTDOMAIN ),
		'id'   => $prefix . 'canvas_transparant',
		'type' => 'checkbox',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Environment Map', TEXTDOMAIN ),
		'desc' => esc_html__( 'Upload an jpeg or Hdr file for environment map. Uses for reflection on metal / glass surface', TEXTDOMAIN ),
		'id'   => $prefix . 'env_map',
		'type' => 'file',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Enable Environment Map as Background', TEXTDOMAIN ),
		'desc' => esc_html__( 'Enable Environment Image', TEXTDOMAIN ),
		'id'   => $prefix . 'env_bg',
		'type' => 'checkbox',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Camera Position', TEXTDOMAIN ),
		'desc' => esc_html__( 'Set Position of camera in scene ( x, y, z )', TEXTDOMAIN ),
		'id'   => $prefix . 'camera_position',
		'type' => 'text',
		'default' => '50, 200, 180',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Camera Rotation', TEXTDOMAIN ),
		'desc' => esc_html__( 'Set Rotation of camera in scene ( x, y, z )', TEXTDOMAIN ),
		'id'   => $prefix . 'camera_rotation',
		'type' => 'text',
		'default' => '0, 0, 45',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Camera Zoom Min', TEXTDOMAIN ),
		'desc' => esc_html__( 'Set Min Zoom Distance of camera in scene.', TEXTDOMAIN ),
		'id'   => $prefix . 'camera_zoom_min',
		'type' => 'text',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Camera Zoom Max', TEXTDOMAIN ),
		'desc' => esc_html__( 'Set Max Zoom Distance of camera in scene.', TEXTDOMAIN ),
		'id'   => $prefix . 'camera_zoom_max',
		'type' => 'text',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Disable Default Lights', TEXTDOMAIN ),
		'desc' => esc_html__( 'Disable lights', TEXTDOMAIN ),
		'id'   => $prefix . 'disable_lights',
		'type' => 'checkbox',
	) );

	$lights = $wc_3d_fields->add_field( array(
		'id'          => $prefix . 'lights',
		'type'        => 'group',
		'description' => esc_html__( 'Add your own custom Lights to scene', TEXTDOMAIN ),
		'options'     => array(
			'group_title'   => esc_html__( 'Light {#}', TEXTDOMAIN ),
			'add_button'    => esc_html__( 'Add Light', TEXTDOMAIN ),
			'remove_button' => esc_html__( 'Remove Light', TEXTDOMAIN ),
			'sortable'      => true,
			'closed'     => true,
		),

	) );

	$wc_3d_fields->add_group_field( $lights, array(
		'name' => esc_html__( 'Select Light Type', TEXTDOMAIN ),
		'desc' => esc_html__( '', TEXTDOMAIN ),
		'id'   => 'light_type',
		'type' => 'select',
		'options' => array(
			'' => __( 'Select Light Type', TEXTDOMAIN ),
			'point' => __( 'Point Light', TEXTDOMAIN ),
			'directional'   => __( 'Directional Light', TEXTDOMAIN ),
			'ambient'   => __( 'Ambient Light', TEXTDOMAIN ),
		),
	) );

	$wc_3d_fields->add_group_field( $lights, array(
		'name' => esc_html__( 'Light Intensity', TEXTDOMAIN ),
		'desc' => esc_html__( 'How bright light needs to be?', TEXTDOMAIN ),
		'id'   => 'light_intensity',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'max' => '1',
			'step' => '0.01',
			'placeholder' => '1'
		),
	) );

	$wc_3d_fields->add_group_field( $lights, array(
		'name' => esc_html__( 'Light Color', TEXTDOMAIN ),
		'desc' => esc_html__( 'Set Light Color', TEXTDOMAIN ),
		'id'   => 'light_color',
		'type' => 'colorpicker',
	) );

	$wc_3d_fields->add_group_field( $lights, array(
		'name' => esc_html__( 'Light Location', TEXTDOMAIN ),
		'desc' => esc_html__( 'Set Position of light in scene ( x, y, z )', TEXTDOMAIN ),
		'id'   => 'light_location',
		'type' => 'text',
		'attributes' => array(
			'placeholder' => '0, 0, 0'
		),
	) );

	$wc_3d_fields->add_group_field( $lights, array(
		'name' => esc_html__( 'Cast Shadow', TEXTDOMAIN ),
		'desc' => esc_html__( '', TEXTDOMAIN ),
		'id'   => 'light_shadow',
		'type' => 'select',
		'options' => array(
			'' => __( 'Select Option', TEXTDOMAIN ),
			'yes' => __( 'Yes', TEXTDOMAIN ),
			'no'   => __( 'No', TEXTDOMAIN ),
		),
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'SVG Graphics', TEXTDOMAIN ),
		'desc' => esc_html__( 'Enable svg material', TEXTDOMAIN ),
		'id'   => $prefix . 'svg_activate',
		'type' => 'checkbox',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Select SVG file', TEXTDOMAIN ),
		'desc' => esc_html__( 'Upload an unwrapped svg texure file here.', TEXTDOMAIN ),
		'id'   => $prefix . 'svg_file',
		'type' => 'file',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'Apply SVG Graphics to Selected Material', TEXTDOMAIN ),
		'id'   => $prefix . 'svg_material_enable',
		'type' => 'checkbox',
	) );

	$wc_3d_fields->add_field( array(
		'name' => esc_html__( 'SVG Material Name', TEXTDOMAIN ),
		'desc' => esc_html__( 'Material Name', TEXTDOMAIN ),
		'id'   => $prefix . 'svg_material',
		'type' => 'text',
		'attributes' => array(
			'data-conditional-id'    => $prefix.'svg_material_enable',
			'data-conditional-value' => 'on',
		),
	) );

	$material_group = $wc_3d_fields->add_field( array(
		'id'          => $prefix . 'material_group',
		'type'        => 'group',
		'description' => esc_html__( 'Select Configuration Options from Below', TEXTDOMAIN ),
		'options'     => array(
			'group_title'   => esc_html__( 'Configuration Group {#}', TEXTDOMAIN ),
			'add_button'    => esc_html__( 'Add Configuration Group', TEXTDOMAIN ),
			'remove_button' => esc_html__( 'Remove Configuration Group', TEXTDOMAIN ),
			'sortable'      => true,
			'closed'     => true,
		),
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name'       => esc_html__( 'Configuration Object Title', TEXTDOMAIN ),
		'id'         => 'mat_obj_title',
		'type'       => 'text',
		//'repeatable' => true,
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name' => esc_html__( 'Configuration Type', TEXTDOMAIN ),
		'desc' => esc_html__( '', TEXTDOMAIN ),
		'id'   => 'config_type',
		'type' => 'select',
		'default' => 'mat',
		'options' => array(
			'mat' => __( 'Material', TEXTDOMAIN ),
			'text'   => __( 'Text', TEXTDOMAIN ),
			'img'   => __( 'Image', TEXTDOMAIN ),
			'gradient'   => __( 'Gradient', TEXTDOMAIN ),
			'pattern'   => __( 'Pattern', TEXTDOMAIN ),
			'object'   => __( 'Object Show/Hide', TEXTDOMAIN ),
			/**
			 * Choice Option to enable select from different options.
			 * Fabric Gallery to enable the Fabric Gallery Picker.
			 */
			'choices'  => __('Choices', TEXTDOMAIN),
			'fabricgallery' => __('Fabric Gallery', TEXTDOMAIN)
		),
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name'        => esc_html__( 'Configuration Id', TEXTDOMAIN ),
		'description' => esc_html__( 'Material Name / SVG Element ID', TEXTDOMAIN ),
		'id'          => 'obj_mat_id',
		'type'        => 'text',
	) );

	$wc_3d_fields = apply_filters( 'wc_3d_config_id_after', $wc_3d_fields, $material_group );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name'        => esc_html__( 'Text Options', TEXTDOMAIN ),
		'description' => esc_html__( 'Advance Text Options', TEXTDOMAIN ),
		'id'          => 'text_options',
		'type'        => 'multicheck_inline',
		'select_all_button' => false,
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'text',
		),
		'options' => array(
			'font' => 'Show Font Family',
			'fontSize' => 'Show Font Size',
			'fontColor' => 'Show Font Color',
		),
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name'        => esc_html__( 'Configuration Options', TEXTDOMAIN ),
		'id'          => 'coptions',
		'type'        => 'title',
	) );
	
	$wc_3d_fields->add_group_field( $material_group, array(
		'name'      => esc_html__( 'Text Color Options', TEXTDOMAIN ),
		'id'        => 'text_color_option',
		'classes' 	=> 'cmb-section-gradient cmb-subsection',
		'type'		=> 'gradient',
		'show_names' => false,
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'text',
		),
		'repeatable'=> 'true',
		'text' 		=> array(
			'add_row_text'    => 'Add Text Color Option',
			'remove_row_text' => 'Remove Text Color Option',
		),
		
	) );

	
	$wc_3d_fields->add_group_field( $material_group, array(
		'name'      => esc_html__( 'Material Options', TEXTDOMAIN ),
		'id'        => 'obj_material_option',
		'classes' 	=> 'cmb-section-material cmb-subsection',
		'type'		=> 'material',
		'show_names' => false,
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'mat',
		),
		'repeatable'=> 'true',
		'text' 		=> array(
			'add_row_text'    => 'Add Material Option',
			'remove_row_text' => 'Remove Material Option',
		),
		
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name' => esc_html__( 'User Upload', TEXTDOMAIN ),
		'desc' => esc_html__( 'Enable user upload', TEXTDOMAIN ),
		'id'   => 'user_upload',
		'type' => 'checkbox',
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'img',
		),
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name'      => esc_html__( 'Pattern Options', TEXTDOMAIN ),
		'id'        => 'obj_pattern_option',
		'classes' 	=> 'cmb-section-image cmb-subsection',
		'type'		=> 'pattern',
		'show_names' => false,
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'pattern',
		),
		'repeatable'=> 'true',
		'text' 		=> array(
			'add_row_text'    => 'Add Pattern Option',
			'remove_row_text' => 'Remove Pattern Option',
		),
		
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name'      => esc_html__( 'Image Options', TEXTDOMAIN ),
		'id'        => 'obj_image_option',
		'classes' 	=> 'cmb-section-image cmb-subsection',
		'type'		=> 'image',
		'show_names' => false,
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'img',
		),
		'repeatable'=> 'true',
		'text' 		=> array(
			'add_row_text'    => 'Add Image Option',
			'remove_row_text' => 'Remove Image Option',
		),
		
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name'        => esc_html__( 'Gradient Options', TEXTDOMAIN ),
		'description' => esc_html__( 'Advance Gradient Options', TEXTDOMAIN ),
		'id'          => 'gradient_options',
		'type'        => 'multicheck_inline',
		'select_all_button' => false,
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'gradient',
		),
		'options' => array(
			'color1' => 'Show Color1',
			'color2' => 'Show Color2',
		),
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name'      => esc_html__( 'Gradient Color Options', TEXTDOMAIN ),
		'id'        => 'obj_gradient_color',
		'classes' 	=> 'cmb-section-gradient cmb-subsection',
		'type'		=> 'gradient',
		'show_names' => false,
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'gradient',
		),
		'repeatable'=> 'true',
		'text' 		=> array(
			'add_row_text'    => 'Add Color Option',
			'remove_row_text' => 'Remove Color Option',
		),
	) );


	$wc_3d_fields->add_group_field( $material_group, array(
		'name'        => esc_html__( 'Object Lists', TEXTDOMAIN ),
		'description' => esc_html__( 'Name of Obj with comma separated including default to show / hide', TEXTDOMAIN ),
		'id'          => 'obj_hide_name',
		'type'        => 'text',
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'object',
		),
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name'      => esc_html__( 'Object Show Options', TEXTDOMAIN ),
		'id'        => 'obj_show_options',
		'classes' 	=> 'cmb-section-object cmb-subsection',
		'type'		=> 'object',
		'show_names' => false,
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'object',
		),
		'repeatable'=> 'true',
		'text' 		=> array(
			'add_row_text'    => 'Add Object Show Option',
			'remove_row_text' => 'Remove Object Show Option',
		),
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name'      => esc_html__( 'Choices', TEXTDOMAIN ),
		'id'        => 'obj_choices_options',
		'type'		=> 'choices',
		'show_names' => false,
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'choices',
		),
		'repeatable'=> 'true',
		'text' 		=> array(
			'add_row_text'    => 'Add Choice',
			'remove_row_text' => 'Remove Choice',
		),
		
	) );

	$wc_3d_fields->add_group_field( $material_group, array(
		'name'      => esc_html__( 'Gallery', TEXTDOMAIN ),
		'id'        => 'obj_fabricgallery_options',
		'type'		=> 'fabricgallery',
		'show_names' => false,
		'attributes' => array(
			'data-conditional-id'    => wp_json_encode( array( $material_group, 'config_type' ) ),
			'data-conditional-value' => 'fabricgallery',
		),
		'repeatable'=> 'true',
		'text' 		=> array(
			'add_row_text'    => 'Add Gallery',
			'remove_row_text' => 'Remove Gallery',
		),
	) );
	
}