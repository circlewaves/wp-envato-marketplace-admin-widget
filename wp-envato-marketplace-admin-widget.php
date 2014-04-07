<?php
/**
 * Plugin Name: WP Envato Marketplace Admin Widget
 * Plugin URI: http://circlewaves.com/products/freebies/wp-envato-marketplace-admin-widget
 * Description: Plugin allows you to verify purchase code right on your WordPress Admin Dashboard.
 * Version: 1.0
 * Author: Circlewaves
 * Author URI: http://circlewaves.com
 * License: GPL2
 */

 
/*
* Functions PREFIX: cw_wpemaw_
*/

	/**
	 * Plugin Settings, used on Plugin Settings page
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	$cw_wpemaw_pluginSettings=array(
		array(
			'name'=>'wpemaw-marketplace-username',
			'title'=>'Your Username',
			'section'=>'main-section'
		),
		array(
			'name'=>'wpemaw-marketplace-api',
			'title'=>'Your API key',
			'section'=>'main-section'
		)		
	);		
 
$cw_wpemaw_plugin_slug='wp-envato-marketplace-admin-widget'; 
$cw_wpemaw_plugin_version='1.0.0'; 
$cw_wpemaw_plugin_screen_hook_suffix=null;

/* Runs when plugin is activated */
register_activation_hook(__FILE__, 'cw_wpemaw_activate'); 
function cw_wpemaw_activate(){
// Activation functionality here
} 

/* Runs when plugin is deactivated */
register_deactivation_hook( __FILE__, 'cw_wpemaw_deactivate' );
function cw_wpemaw_deactivate(){
// Deactivation functionality here
} 


/**
 * Add settings action link to the plugins page.
 *
 * @since    1.0.0
 */

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'cw_wpemaw_add_action_links' );
function cw_wpemaw_add_action_links( $links ) {
global $cw_wpemaw_plugin_slug;
	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $cw_wpemaw_plugin_slug ) . '">' . __( 'Settings', $cw_wpemaw_plugin_slug ) . '</a>'
		),
		$links
	);
}		

// Load admin style sheet and JavaScript.
add_action( 'admin_enqueue_scripts', 'cw_wpemaw_enqueue_admin_styles_and_scripts' );
function cw_wpemaw_enqueue_admin_styles_and_scripts() {
	global $cw_wpemaw_plugin_screen_hook_suffix;
	global $cw_wpemaw_plugin_slug;
	// Include styles
	wp_enqueue_style( $cw_wpemaw_plugin_slug.'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), $cw_wpemaw_plugin_version );
	
	// Include scripts
	if ( ! isset( $cw_wpemaw_plugin_screen_hook_suffix ) ) {
		return;
	}
	$screen = get_current_screen();	
	if(( $cw_wpemaw_plugin_screen_hook_suffix == $screen->id )) {
	//	wp_enqueue_script('jquery');
	//	wp_enqueue_script( $cw_wpemaw_plugin_slug.'-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery'), $cw_wpemaw_plugin_version);		
	}
}

/* Add admin menu*/
add_action( 'admin_menu', 'cw_wpemaw_add_plugin_admin_menu');
function cw_wpemaw_add_plugin_admin_menu() {
global $cw_wpemaw_plugin_slug;
global $cw_wpemaw_plugin_screen_hook_suffix;
	/*
	 * Add a settings page for this plugin to the Settings menu.
	 */
		$cw_wpemaw_plugin_screen_hook_suffix=add_options_page(
		__( 'Envato Marketplace Widget Options', $cw_wpemaw_plugin_slug ),
		__( 'Envato Marketplace Options', $cw_wpemaw_plugin_slug ),
		'manage_options',
		$cw_wpemaw_plugin_slug,
		'cw_wpemaw_display_plugin_admin_page'
	);
	 
}

/**
 * Render the settings page for this plugin.
 *
 * @since    1.0.0
 */
function cw_wpemaw_display_plugin_admin_page() {
global $cw_wpemaw_plugin_slug;
global $cw_wpemaw_pluginSettings;
	include_once( 'views/admin.php' );
}


/**
 * Render settings page
 */	
add_action( 'admin_init', 'cw_wpemaw_admin_options_init' );	
/**
 * Init plugin options
 *
 * @since    1.0.0
 */	
function cw_wpemaw_admin_options_init() {
global $cw_wpemaw_plugin_slug;
global $cw_wpemaw_plugin_screen_hook_suffix;
global $cw_wpemaw_pluginSettings;

	// Sections
	add_settings_section( 'main-section', 'Please enter your Envato Marketplace username and your API key', 'cw_wpemaw_main_section_callback', $cw_wpemaw_plugin_slug );

	// Handle plugin options
	foreach($cw_wpemaw_pluginSettings as $setting){
		// Register Settings
		register_setting( 'cw_wpemaw_settings', $setting['name'] );
		
		// Fields
		add_settings_field( $setting['name'], $setting['title'], 'cw_wpemaw_setting_field_callback', $cw_wpemaw_plugin_slug, $setting['section'], array('name'=>$setting['name']) );
	}

 
}	

/**
 * Main section callback
 *
 * @since    1.0.0
 */	
function cw_wpemaw_main_section_callback() {
	// some actions here;
}


/**
 * Generate setting field
 *
 * @since    1.0.0
 */	
function cw_wpemaw_setting_field_callback($args) {
	$setting_value = esc_attr( get_option( $args['name'] ) );
	echo '<input class="regular-text" type="text" name="'.$args['name'].'" value="'.$setting_value.'" />';
}
		


/* Add admin widget*/
add_action( 'wp_dashboard_setup', 'cw_wpemaw_add_plugin_admin_widget');
function cw_wpemaw_add_plugin_admin_widget() {
global $cw_wpemaw_plugin_slug;
global $cw_wpemaw_plugin_screen_hook_suffix;
global $cw_wpemaw_pluginSettings;
global $wp_meta_boxes;
	wp_add_dashboard_widget('cw_wpemaw_admin_widget', __('Envato Marketplace Widget',$cw_wpemaw_plugin_slug), 'cw_wpemaw_display_plugin_admin_widget');
	 
}

/**
 * Render the admin widget
 *
 * @since    1.0.0
 */
function cw_wpemaw_display_plugin_admin_widget() {
global $cw_wpemaw_plugin_slug;
global $cw_wpemaw_plugin_screen_hook_suffix;
global $cw_wpemaw_pluginSettings;
	include_once( 'views/admin_widget.php' );
}


?>