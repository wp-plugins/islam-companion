<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link:       http://nadirlatif.me/islam-companion
 * @since      1.0.0
 *
 * @package    Islam_Companion
 * @subpackage Islam_Companion/includes
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Islam_Companion
 * @subpackage Islam_Companion/admin
 * @author:       Nadir Latif <nadir@nadirlatif.me>
 */
class Islam_Companion_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name    The ID of this plugin.
	 */
	private $name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $version ) {

		$this->name = $name;
		$this->version = $version;

	}

  
	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Islam_Companion_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Islam_Companion_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->name, plugin_dir_url( __FILE__ ) . 'css/islam-companion-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Islam_Companion_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Islam_Companion_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->name, plugin_dir_url( __FILE__ ) . 'js/islam-companion-admin.js', array( 'jquery' ), $this->version, FALSE );

	}

	/**
	 * Function for creating the settings page.
	 *
	 * @since    1.0.0
	 */
	public function CreateSettingsPage() {

		$islam_companion_settings_class = new IslamCompanionSettingsClass();
		
	}
	
	
	/**
	 * Function for executing functions that need to be called in the admin_notices hook
	 *
	 * @since    1.0.0
	 */
	public function admin_notices_hooks() {		

		$message_for_the_day = new IC_MessageForTheDay();
		$message_for_the_day->AdminNotices();	
		
	}
	
	/**
	 * Function for executing functions that need to be called in the admin_head hook
	 *
	 * @since    1.0.0
	 */
	public function admin_head_hooks() {		
		
		$message_for_the_day = new IC_MessageForTheDay();
		$message_for_the_day->AdminHead();	
		
	}
}
