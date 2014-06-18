<?php
/**
 * Plugin Name: WP Envato Marketplace Admin Widget
 * Plugin URI: http://circlewaves.com/products/freebies/wp-envato-marketplace-admin-widget
 * Description: Plugin allows you to verify purchase code right on your WordPress Admin Dashboard.
 * Version: 1.1.0
 * Author: Circlewaves
 * Author URI: http://circlewaves.com
 * License: GPL2
 */
 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . '/class-wp-envato-marketplace-admin-widget.php' );
	
register_activation_hook( __FILE__, array( 'WP_Envato_Marketplace_Admin_Widget', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Envato_Marketplace_Admin_Widget', 'deactivate' ) ); 




	add_action( 'plugins_loaded', array( 'WP_Envato_Marketplace_Admin_Widget', 'get_instance' ) );

}

?>