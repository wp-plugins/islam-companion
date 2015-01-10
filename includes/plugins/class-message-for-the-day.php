<?php

/**
 * The file that defines the "Message For The Day" feature
 *
 * A class definition that includes attributes and functions used on the admin dashboard.
 *
 * @link:       http://nadirlatif.me/islam-companion
 * @since      1.0.0
 *
 * @package    Islam_Companion
 * @subpackage Islam_Companion/includes
 */

/**
 * The class for "Message for the day" feature.
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
class IC_MessageForTheDay {
	/**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {

		// Set class property
        $this->options = get_option( 'ic_options' );
        
	}

	/**
	 * Returns a random Quranic verse from the Holy Quran for the given language and translator
	 *
	 * @since    1.0.0
	 */
	private function DisplayVerseText() {
		
		global $wp_meta_boxes;

		wp_add_dashboard_widget('message-for-the-day-widget', 'Holy Quran Message', array($this,'CustomDashBoardText'));
		
	}
	
	/**
	 * Returns a random Quranic verse from the Holy Quran for the given language and translator
	 *
	 * @since    1.0.0
	 */
	public function CustomDashBoardText( $post, $callback_args ) {
		
		$encryption = new Encryption();
			   
	    list($current_language,$current_narrator)=explode("~",$this->options['ic_narrator']);
	    
		$verse_text=file_get_contents("http://nadirlatif.me/scripts/api.php?option=".urlencode(base64_encode("get_randon_verse"))."&lang=".urlencode(base64_encode($current_language))."&narrator=".urlencode(base64_encode($current_narrator)));
		$verse_text=$encryption->DecryptText($verse_text);
		$verse_text=wptexturize( $verse_text );
		
		$is_language_rtl=file_get_contents("http://nadirlatif.me/scripts/api.php?option=".urlencode(base64_encode("is_language_rtl"))."&lang=".urlencode(base64_encode($current_language)));
		$is_language_rtl=trim($encryption->DecryptText($is_language_rtl));
		
		$rtl=($is_language_rtl=='true')?'right':'left';
		
		echo "<p style='text-align:".$rtl."'>".$verse_text."</p>";
	}
	
	
	/**
	 * Function that need to be called in the admin_notices hook
	 *
	 * @since    1.0.0
	 */
	public function WPDashBoardSetupHook() {		
		
		$this->DisplayVerseText();
		
	}
	
	
	/**
	 * Function that is used to add a sub menu
	 *
	 * @since    1.0.0
	 */
	public function AddSubMenu() {		
			
		
	}
}
?>