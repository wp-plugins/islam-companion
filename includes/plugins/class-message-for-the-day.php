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
		global $wpdb;

		$encryption = new Encryption();
			   
	    list($current_language,$current_narrator)=explode("~",$this->options['ic_narrator']);
	    
		$verse_text=file_get_contents("http://nadirlatif.me/scripts/api.php?lang=".urlencode(base64_encode($current_language))."&narrator=".urlencode(base64_encode($current_narrator)));
		
		$verse_text=$encryption->DecryptText($verse_text);

		$verse_text=wptexturize( $verse_text );
		echo "<p id='ic_message_for_the_day'>".$verse_text."</p>";
		
	}
	
	/**
	 * Returns a random Quranic verse from the Holy Quran for the given language and translator
	 *
	 * @since    1.0.0
	 */
	private function DisplayVerseTextCSS() {
		
		global $wpdb;
	    $current_language=$this->options['ic_language'];
	    $rows = $wpdb->get_results( "SELECT rtl FROM qa_quranic_text_meta WHERE language='".mysql_escape_string($current_language)."'" );
		
		// This makes sure that the positioning is also good for right-to-left languages
		$x = $rows[0]->rtl ? 'right' : 'left';
	
		echo "
		<style type='text/css'>
		#ic_message_for_the_day {
			float: $x;
			padding-$x: 15px;
			padding-top: 5px;		
			margin: 0;
			font-size: 11px;
		}
		</style>
		";
	}
	
	/**
	 * Function that need to be called in the admin_notices hook
	 *
	 * @since    1.0.0
	 */
	public function AdminNotices() {		
		
		$this->DisplayVerseText();
		
	}
	
	/**
	 * Function that need to be called in the admin_head hook
	 *
	 * @since    1.0.0
	 */
	public function AdminHead() {		
		
		$this->DisplayVerseTextCSS();
		
	}
}
?>