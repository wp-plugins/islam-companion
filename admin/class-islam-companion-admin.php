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
     * Holds the plugin objects
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $plugin_objects   The objects of plugin classes.
     */
    private $plugin_objects;
	
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
	 * Executes a function of the given plugin class, or all plugins classes
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_class       The name of the plugin class.
	 * @var      string    $function_name    The name of the function.
	 */
	private function execute_plugin_function($plugin_class,$function_name) {

		try
			{
				foreach($this->plugin_objects as $temp_plugin_class=>$plugin_object)
					{
						if($temp_plugin_class==$plugin_class||$plugin_class=="All")
							{
								if(method_exists($plugin_object, $function_name))$plugin_object->$function_name();
							}
					}
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
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
		try
			{
				wp_enqueue_style( $this->name, plugin_dir_url( __FILE__ ) . 'css/islam-companion-admin.css', array(), $this->version, 'all' );
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
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

		try
			{
				wp_enqueue_script( $this->name, plugin_dir_url( __FILE__ ) . 'js/islam-companion-admin.js', array( 'jquery' ), $this->version, FALSE );
				wp_localize_script( $this->name, 'objectL10n', array(
					'language_alert' => __( "Please select a language", "islam-companion" ),
					'narrator_alert' => __( "Please select a narrator", "islam-companion" ),
					'sura_alert' => __( "Please select a sura", "islam-companion" ),
					'ruku_alert' => __( "Please select a ruku", "islam-companion" ),
					'selected_text_alert' => __( "Please select a word", "islam-companion" ),
					'data_fetch_alert' => __( "An error occurred while trying to get data from server. Please try again", "islam-companion" ),
					'language_select_text' => __( "Please select a language first", "islam-companion" ),
					'sura_select_text' => __( "Please select a sura first", "islam-companion" ),
					'select_text' => __( "Please Select", "islam-companion" ),
				));
				
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}

	/**
	 * Function for creating the settings page.
	 *
	 * @since    1.0.0
	 */
	public function CreateSettingsPage() {
			
		try
			{
				$islam_companion_settings_class = new IslamCompanionSettingsClass($this);
				$this->GetPluginObjects();
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
	
	/**
	 * Function for creating the admin menus.
	 *
	 * @since    1.0.0
	 */
	public function create_custom_menu() {
			
		// This will add a new top level menu called "Islam Companion"
		//add_menu_page( "Islam Companion", "Islam Companion", "manage_options", "?page=islam-companion&option=my-holy-quran", "", "/wp-content/plugins/islam-companion/admin/images/holy-quran.png");
		
		//$this->execute_plugin_function("All","AddSubMenu");
	}
	
	
	/**
	 * Function used to return plugin objects
	 * All plugin objects have certain common functions. e.g creating sub menus 
	 *
	 * @since    1.0.0
	 * @var      object    $islam_companion_settings_class The $islam_companion_settings_class object
	 */
	private function GetPluginObjects() {		
		try
			{
				$plugin_folder_name=WP_PLUGIN_DIR.DIRECTORY_SEPARATOR."islam-companion/includes/plugins";
				
				$file_list=scandir($plugin_folder_name);
				$this->plugin_objects=array();
				for($count=0;$count<count($file_list);$count++)
					{
						$file_name=$file_list[$count];
						if($file_name=="."||$file_name==".."||$file_name=="class-encryption.php"||$file_name=="class-logging.php")continue;
						
						include($plugin_folder_name.DIRECTORY_SEPARATOR.$file_name);
						$class_name=str_replace("class-", "", $file_name);
						$class_name=str_replace("-"," ",$class_name);
						$class_name=ucwords($class_name);
						$class_name=str_replace(" ", "", $class_name);
						$class_name=str_replace(".php", "", $class_name);
						$class_name="IC_".$class_name;
						$this->plugin_objects[$class_name]=new $class_name();
					}		
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
	
	/**
	 * Function for executing plugin functions that need to be called in the admin_notices hook
	 * url: http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	 *
	 * @since    1.0.0
	 */
	public function wp_dashboard_setup_hooks() {		

		try
			{
				$this->execute_plugin_function("All","WPDashBoardSetupHook");		
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
	
	/**
	 * Function for executing functions that need to be called in the admin_head hook
	 * url: http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	 *
	 * @since    1.0.0
	 */
	public function admin_head_hooks() {		
		
		try
			{
				$this->execute_plugin_function("All","AdminHeadHook");
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
	
	/**
	 * Function for executing functions that need to be called in the wp_ajax_islam_companion hook
	 * url: http://codex.wordpress.org/AJAX_in_Plugins
	 *
	 * @since    1.0.3
	 */
	public function islam_companion_callback() {		
		
		try
			{
				if(!isset($_POST['plugin'])||!isset($_POST['action']))wp_die();
				
				check_ajax_referer('islam-companion', 'security' );
				
				$plugin_name=$_POST['plugin'];
				$this->execute_plugin_function($plugin_name,"IslamCompanionAjax");
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
	
}
