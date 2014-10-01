<?php
/**
 * WP Envato Marketplace Admin Widget
 *
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * @package   WP_Envato_Marketplace_Admin_Widget
 * @author    Circlewaves Team <support@circlewaves.com>
 * @license   GPL-2.0+
 * @link      http://circlewaves.com
 * @copyright 2014 Circlewaves Team <support@circlewaves.com>
 */


 class WP_Envato_Marketplace_Admin_Widget{
 
 
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.2.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'wp-envato-marketplace-admin-widget';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	
	/**
	 * Plugin Settings, used on Plugin Settings page
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	public static $pluginSettings=array(
		array(
			'name'=>'wpemaw-marketplace-username',
			'title'=>'Your Username',
			'section'=>'main-section',
			'field'=>array(
				'type'=>'text'
			)
		),
		array(
			'name'=>'wpemaw-marketplace-api',
			'title'=>'Your API key',
			'section'=>'main-section',
			'field'=>array(
				'type'=>'text'
			)
		),
		array(
			'name'=>'wpemaw-marketplace-stat-admin-only',
			'title'=>'Show balance only for admin',
			'section'=>'main-section',
			'field'=>array(
				'type'=>'checkbox',
				'description'=>'If checked - only purchase verification form will be displayed for non-admin users'
			)
		)	
	);		
	
	/**
	 * Plugin Settings Values
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	public static $pluginDefaultSettings=array(
		'plugin-version'=>array(
			'name'=>'wpemaw-plugin-version',
			'value'=>'1.1.0'
		)
	);
	
	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
	
		if((current_user_can('edit_posts'))){ // Load widget only if current user can "edit_posts"

				// Load plugin text domain
				add_action( 'init', array( $this, 'widget_textdomain' ) );
				
				// Activate plugin when new blog is added
				add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );
				
				// Load admin stylesheets and JavaScript
				add_action( 'admin_enqueue_scripts', array($this,'enqueue_admin_styles_and_scripts') );
				
				// Add the options page and menu item.
				add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
				add_action( 'admin_init', array( $this, 'admin_options_init' ) );	
			
				// Add an action link pointing to the options page.
				$plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . $this->plugin_slug . '.php' );
				add_filter( 'plugin_action_links_' . $plugin_basename, array($this, 'add_action_links') );

				// Handle admin view
				add_action( 'wp_dashboard_setup', array($this,'add_plugin_admin_widget'));	
			}

	}
	
	

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
			
		//Always update plugin version
		update_option( self::$pluginDefaultSettings['plugin-version']['name'], self::$pluginDefaultSettings['plugin-version']['value'] );

	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {

	}
	
	
	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE,  plugin_dir_path( __FILE__ ) . '/lang/' );	


	} // end widget_textdomain

	
	
/*--------------------------------------------------*/
/* Public Functions
/*--------------------------------------------------*/


/**
 * Add settings action link to the plugins page.
 *
 * @since    1.0.0
 */
function add_action_links( $links ) {
	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
		),
		$links
	);
}		

// Load admin style sheet and JavaScript.
function enqueue_admin_styles_and_scripts() {
	// Include styles
	wp_enqueue_style( $this->plugin_slug.'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), self::VERSION );
	

	$screen = get_current_screen();	

	if(( $screen->id == 'dashboard')) { // load scripts if current screen is the wp-dashboard
	
		//Include jQuery UI theme css
		wp_enqueue_style( $this->plugin_slug .'-jquery-ui', plugins_url( 'assets/css/jquery-ui/jquery-ui-1.10.4.custom.min.css', __FILE__ ), array(), self::VERSION );
	
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-tabs');
		
		wp_enqueue_script( $this->plugin_slug.'-jquery-cookie', plugins_url( 'assets/js/jquery.cookie.js', __FILE__ ), array( 'jquery' ), self::VERSION);		
		wp_enqueue_script( $this->plugin_slug.'-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery','jquery-ui-tabs',$this->plugin_slug.'-jquery-cookie' ), self::VERSION);		
	}
	
	// Include scripts
	if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
		return;
	}
}



/* Add admin menu*/
function add_plugin_admin_menu() {
	/*
	 * Add a settings page for this plugin to the Settings menu.
	 */
		$this->plugin_screen_hook_suffix=add_options_page(
		__( 'Envato Marketplace Admin Widget Options', $this->plugin_slug ),
		__( 'Marketplace Widget Options', $this->plugin_slug ),
		'manage_options',
		$this->plugin_slug,
		array( $this, 'display_plugin_admin_page' )
	);
	 
}

/**
 * Render the settings page for this plugin.
 *
 * @since    1.0.0
 */
function display_plugin_admin_page() {
	include_once( 'views/admin.php' );
}


/**
 * Init plugin options
 *
 * @since    1.0.0
 */	
function admin_options_init() {
	// Sections
	add_settings_section( 'main-section', 'Please enter your Envato Marketplace username and your API key', array($this,'main_section_callback'), $this->plugin_slug );

	// Handle plugin options
	foreach(self::$pluginSettings as $setting){
		// Register Settings
		register_setting( 'cw_wpemaw_settings', $setting['name'] );
		
		// Fields
		add_settings_field( $setting['name'], $setting['title'], array($this,'setting_field_callback'), $this->plugin_slug, $setting['section'],  array('name'=>$setting['name'],'field'=>$setting['field']) );
	}
	

 
}	



/**
 * Main section callback
 *
 * @since    1.0.0
 */	
function main_section_callback() {
	// some actions here;
}


/**
 * Generate setting field
 *
 * @since    1.0.0
 */	
function setting_field_callback($args) {
	$setting_value =  isset($args['value'])?$args['value']:get_option( $args['name'] ) ;
	$field=$args['field'];
	$field_descr=isset($field['description'])?('<span class="description">'.$field['description'].'</span>'):'';		
		switch($field['type']){
			case 'checkbox':
			?>
				<input type="checkbox" name="<?php echo $args['name'];?>" value="1" <?php checked( $setting_value, '1', true);?> />
				<?php echo $field_descr;?>
			<?php
			break;
			case 'text':
			?>
				<input class="regular-text" type="text" name="<?php echo $args['name'];?>" id="<?php echo $args['name'];?>" value="<?php echo esc_attr($setting_value);?>" />
				<?php echo $field_descr;?>
			<?php	
			break;
	}
}
		


/* Add admin widget*/
function add_plugin_admin_widget() {
global $wp_meta_boxes;
	wp_add_dashboard_widget('cw_wpemaw_admin_widget', __('Envato Marketplace Widget',$this->plugin_slug), array($this,'display_plugin_admin_widget'));
	 
}

/**
 * Render the admin widget
 *
 * @since    1.0.0
 */
function display_plugin_admin_widget() {
	include_once( 'views/admin_widget.php' );
}

}
?>