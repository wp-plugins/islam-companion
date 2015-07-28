<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link:       http://nadirlatif.me/islam-companion
 * @since      1.0.0
 *
 * @package    Islam_Companion
 * @subpackage Islam_Companion/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Islam_Companion
 * @subpackage Islam_Companion/includes
 * @author:       Nadir Latif <nadir@nadirlatif.me>
 */
class Islam_Companion {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Islam_Companion_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $Islam_Companion    The string used to uniquely identify this plugin.
	 */
	protected $Islam_Companion;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		try
			{
				$this->plugin_name = 'islam-companion';
				$this->version = '1.0.5';
		
				$this->load_dependencies();
				$this->set_error_handling();
				$this->set_locale();
				$this->define_admin_hooks();
				$this->define_public_hooks();
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Islam_Companion_Loader. Orchestrates the hooks of the plugin.
	 * - Islam_Companion_i18n. Defines internationalization functionality.
	 * - Islam_Companion_Admin. Defines all hooks for the dashboard.
	 * - Islam_Companion_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		
		try
			{
				/**
				 * The class responsible for logging/error handling
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/plugins/class-logging.php';
				/**
				 * The class responsible for encryption/decryption
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/plugins/class-encryption.php';
				/**
				 * The class responsible for constructing the settings page of the plugin
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-islam-companion-settings.php';
				
				/**
				 * The class responsible for orchestrating the actions and filters of the
				 * core plugin.
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-islam-companion-loader.php';
		
				/**
				 * The class responsible for defining internationalization functionality
				 * of the plugin.
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-islam-companion-i18n.php';
		
				/**
				 * The class responsible for defining all actions that occur in the Dashboard.
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-islam-companion-admin.php';
		
				/**
				 * The class responsible for defining all actions that occur in the public-facing
				 * side of the site.
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-islam-companion-public.php';
		
				$this->loader = new Islam_Companion_Loader();
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}

	/**
	 * Used to set error handling functions.
	 *
	 * Sets the functions that should be called automatically when an error or exception occurs
	 *
	 * @since    1.0.5
	 * @access   private
	 */
	private function set_error_handling() {

		try
			{
				//$mail_from=ini_get("sendmail_from");
				define("DEBUG",false);
				define("LOG_ERROR_EMAIL", 'nadir@pakjiddat.com');
				define("LOG_ERROR_HEADER", "Subject: Error occured in Islam Companion Plugin. Please Check!\n");
				define("LOG_FILE_NAME", "");
				if(!DEBUG&&!defined("API_URL"))define("API_URL","http://pakjiddat.com/scripts/api.php");
				else if(!defined("API_URL"))define("API_URL","http://dev.pakjiddat.com/scripts/api.php");
				
				$logger=new Logger();
				set_error_handler(array($logger,"ErrorHandler"));
				set_exception_handler(array($logger,"ExceptionHandler"));
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
	
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Islam_Companion_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		try
			{
				$plugin_i18n = new Islam_Companion_i18n();
				$plugin_i18n->set_domain( $this->get_plugin_name() );

				$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
			
		try
			{
				$plugin_admin = new Islam_Companion_Admin( $this->get_plugin_name(), $this->get_version() );

				$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
				$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
			  	$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'wp_dashboard_setup_hooks' );
				$this->loader->add_action( 'admin_head', $plugin_admin, 'admin_head_hooks' );
				$this->loader->add_action( 'wp_ajax_islam_companion', $plugin_admin, 'islam_companion_callback' );
				// The settings page for the plugin is created
				if( is_admin() )$plugin_admin->CreateSettingsPage();
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		try
			{
				$plugin_public = new Islam_Companion_Public( $this->get_plugin_name(), $this->get_version() );

				$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
				$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Islam_Companion_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
