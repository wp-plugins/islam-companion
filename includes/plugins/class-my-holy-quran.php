<?php

/**
 * The file that defines the "My Holy Quran" feature
 *
 * A class definition that includes attributes and functions used on the admin dashboard.
 *
 * @link:       http://nadirlatif.me/islam-companion
 * @since      2.0.0
 *
 * @package    Islam_Companion
 * @subpackage Islam_Companion/includes
 */

/**
 * The class for "My Holy Quran" feature.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    Islam_Companion
 * @subpackage Islam_Companion/includes
 * @author:       Nadir Latif <nadir@nadirlatif.me>
 */
class IC_MyHolyQuran {
	/**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @var      string    $name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {

		// Set class property
        $this->options = get_option( 'ic_options' );
        
	}
	
	/**
	 * Function that is used to add a sub menu
	 *
	 * @since    2.0.0
	 */
	public function AddSubMenu() {		
		
		add_submenu_page( "?page=islam-companion&option=my-holy-quran", "My Holy Quran", "My Holy Quran", "manage_options", "?page=islam-companion&option=my-holy-quran");
		add_submenu_page( "?page=islam-companion&option=my-holy-quran", "Bookmarks", "Bookmarks", "manage_options", "?page=islam-companion&option=bookmarks", "");
		
	}
}
?>