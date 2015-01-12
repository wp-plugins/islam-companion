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

		wp_add_dashboard_widget('message-for-the-day-widget', 'Holy Quran', array($this,'CustomDashBoardText'));
		
	}
	
	
	/**
	 * Sets default options if no options were set by the user
	 *
	 * @since    1.0.2
	 */
	private function SetDefaultOptions() {
		
		if(!isset($this->options['ic_narrator'])||(isset($this->options['ic_narrator'])&&$this->options['ic_narrator']==''))$this->options['ic_narrator']="Mohammed Marmaduke William Pickthall";
		if(!isset($this->options['ic_language'])||(isset($this->options['ic_language'])&&$this->options['ic_language']==''))$this->options['ic_language']="English";
		if(!isset($this->options['ic_sura'])||(isset($this->options['ic_sura'])&&$this->options['ic_sura']==''))$this->options['ic_sura']="Al-Faatiha~7";
		if(!isset($this->options['ic_aya'])||(isset($this->options['ic_aya'])&&$this->options['ic_aya']==''))$this->options['ic_aya']="1";
		if(!isset($this->options['ic_ayat_count'])||(isset($this->options['ic_ayat_count'])&&$this->options['ic_ayat_count']==''))$this->options['ic_ayat_count']="5";
	}
	
	/**
	 * Returns a random Quranic verse from the Holy Quran for the given language and translator
	 *
	 * @since    1.0.0
	 */
	public function CustomDashBoardText( $post, $callback_args ) {
		
		$encryption = new Encryption();
		
		$this->SetDefaultOptions();
		
	    list($current_language,$current_narrator)=explode("~",$this->options['ic_narrator']);
	    $current_language=$this->options['ic_language'];
		list($current_sura,$ayat_count)=explode("~",$this->options['ic_sura']);
		$current_aya=$this->options['ic_aya'];
		$current_aya_count=$this->options['ic_ayat_count'];
		
		$verse_text=file_get_contents("http://nadirlatif.me/scripts/api.php?option=".urlencode(base64_encode("get_sura_verses"))."&lang=".urlencode(base64_encode($current_language))."&narrator=".urlencode(base64_encode($current_narrator))."&sura=".urlencode(base64_encode($current_sura))."&aya=".urlencode(base64_encode($current_aya))."&aya_count=".urlencode(base64_encode($current_aya_count)));
		$verse_text=$encryption->DecryptText($verse_text);
		$verse_text=wptexturize( $verse_text );
		
		$is_language_rtl=file_get_contents("http://nadirlatif.me/scripts/api.php?option=".urlencode(base64_encode("is_language_rtl"))."&lang=".urlencode(base64_encode($current_language)));
		$is_language_rtl=trim($encryption->DecryptText($is_language_rtl));
		
		$rtl=($is_language_rtl=='true')?'right':'left';
		$direction=($is_language_rtl=='true')?'rtl':'ltr';
	
		$ayat_text_str="";
		$ayat_text_arr=explode("~",$verse_text);
		
		for($count=0;$count<count($ayat_text_arr);$count++)
			{
				if($rtl=="left")$ayat_text_str.="<li>".$ayat_text_arr[$count]."</li>";	
				else $ayat_text_str.=$ayat_text_arr[$count]."</br>";
			}
			
		if($rtl=="left")$ayat_text_str="<ol>".$ayat_text_str."</ol>";	
		
		if($rtl=="right")echo "<p style='direction:".$direction.";text-align:".$rtl.";word-wrap: break-word;font-size:120%;font-weight: bold;'>".$ayat_text_str."</p>";
		else echo "<p style='direction:".$direction.";text-align:".$rtl.";word-wrap: break-word;'>".$ayat_text_str."</p>";
		
		//echo "<b><a href='#' onclick='FetchVerseData();'>&laquo; prev</a> | <a href='#' onclick='FetchVerseData();'>next &raquo;</a></b><br/>";
		echo "<p><b>Surah: ".$current_sura." (verse ".$current_aya."-".($current_aya+$current_aya_count).")</b></p>";
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