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
	 * Gets the default options configured by the user
	 *
	 * @since    1.0.3
	 * @return   array    The list of options configured by the user
	 */
	private function GetDefaultOptions() {
		
		list($current_language,$current_narrator)=explode("~",$this->options['ic_narrator']);
	    $current_language=$this->options['ic_language'];
		list($current_sura,$total_ayat_count)=explode("~",$this->options['ic_sura']);
		$current_aya=$this->options['ic_aya'];
		$current_aya_count=$this->options['ic_ayat_count'];

		$url="http://nadirlatif.me/scripts/api.php?option=".urlencode(base64_encode("get_sura_verses"))."&lang=".urlencode(base64_encode($current_language))."&narrator=".urlencode(base64_encode($current_narrator))."&sura=".urlencode(base64_encode($current_sura))."&aya={aya}&aya_count=".urlencode(base64_encode($current_aya_count));

		$current_url=str_replace("{aya}",urlencode(base64_encode($current_aya)),$url);
		
		$parsed_options=array("total_ayat_count"=>$total_ayat_count,"current_language"=>$current_language,
		"current_narrator"=>$current_narrator,"current_sura"=>$current_sura,"current_aya"=>$current_aya,
		"current_aya_count"=>$current_aya_count,"current_url"=>$current_url,"url"=>$url);
		
		return $parsed_options; 
	}
	
	/**
	 * Returns a random Quranic verse from the Holy Quran for the given language and translator
	 *
	 * @since    1.0.0
	 */
	public function CustomDashBoardText( $post, $callback_args ) {				
		
		$this->SetDefaultOptions();
		
		$parsed_options=$this->GetDefaultOptions();
	    
		$verse_text=file_get_contents($parsed_options['current_url']);
		$verse_text_str=$this->FormatVerseDataForWPDashboard($verse_text,$parsed_options);
		echo $verse_text_str;
	}
	
	/**
	 * Function that is used to decrypt the Quranic verse data
	 * The decrypted data is returned as string
	 *
	 * @since    1.0.3
	 * @var      string    $verse_text       The encrypted verse text.
	 * @var      array    $parsed_options       The options configured by the user.
	 * @return    string    The decryped string. suitable for displaying on the dashboard
	 */
	private function FormatVerseDataForWPDashboard($verse_text,$parsed_options)
		{
			$encryption = new Encryption();
			
			$verse_text=$encryption->DecryptText($verse_text);
			$verse_text=wptexturize( $verse_text );
			
			$is_language_rtl=file_get_contents("http://nadirlatif.me/scripts/api.php?option=".urlencode(base64_encode("is_language_rtl"))."&lang=".urlencode(base64_encode($parsed_options['current_language'])));
			$is_language_rtl=trim($encryption->DecryptText($is_language_rtl));
			
			$rtl=($is_language_rtl=='true')?'right':'left';
			$direction=($is_language_rtl=='true')?'rtl':'ltr';
			$navigator_direction=($is_language_rtl=='true')?'left':'right';
			
			$ajax_nonce = wp_create_nonce("islam-companion");
			$end_aya=(($parsed_options['current_aya']+$parsed_options['current_aya_count']-1)>$parsed_options['total_ayat_count'])?$parsed_options['total_ayat_count']:($parsed_options['current_aya']+$parsed_options['current_aya_count']-1);
			$ayat_text_str="";
			$ayat_text_arr=explode("~",$verse_text);
			
			$previous_link=$next_link=$separator="";
			if($parsed_options['current_aya']>1)$previous_link="<div style='font-size: 10pt;direction:ltr;float: ".$navigator_direction."'><b><a href='#' onclick='FetchVerseData(\"".$ajax_nonce."\",\"prev\");'>&laquo; prev</a>";
	 		else $previous_link="</div><br/>";
					
			if($end_aya<$parsed_options['total_ayat_count'])$next_link="<a href='#' onclick='FetchVerseData(\"".$ajax_nonce."\",\"next\");'>next &raquo;</a></b></div><br/>";
			else $next_link="</div><br/>";
									
			if($parsed_options['current_aya']>1&&$end_aya<$parsed_options['total_ayat_count'])$separator=" <span>|</span> ";
			
			for($count=0;$count<count($ayat_text_arr);$count++)
				{
					if($rtl=="left")$ayat_text_str.="<li>".$ayat_text_arr[$count]."</li>";	
					else $ayat_text_str.=$ayat_text_arr[$count]."</br>";
				}
								
			if($rtl=="left")$ayat_text_str="<ol>".$ayat_text_str."</ol>";	
			
			$verse_text_str="";
			
			if($rtl=="right")$verse_text_str.="<div id='ic-quran-dashboard-text' style='line-height:25px;font-size: 13pt;direction:".$direction.";text-align:".$rtl.";word-wrap: break-word;font-weight: bold;'>".$ayat_text_str;
			else $verse_text_str.="<div id='ic-quran-dashboard-text' style='direction:".$direction.";text-align:".$rtl.";word-wrap: break-word;'>".$ayat_text_str;
			
			$verse_text_str.=$previous_link.$separator.$next_link;
			$verse_text_str.="<hr/>";
			$verse_text_str.="<p><b>Surah: ".$parsed_options['current_sura'].", verse <span>".$parsed_options['current_aya']."</span>-<span>".$end_aya."</span> of <span id='ic-total-ayat-count'>".$parsed_options['total_ayat_count']."</span></b></p></div>";
			return $verse_text_str;	
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
	
	/**
	 * Function that is used to add a sub menu
	 *
	 * @since    1.0.3
	 */
	public function IslamCompanionAjax() {		
			
		$plugin_action=$_POST['plugin_action'];		
		
		if($plugin_action=='fetch_verse_data')
			{
				$action=$_POST['direction'];
				if($action=="next"){$this->options['ic_aya']+=$this->options['ic_ayat_count'];update_option("ic_options",$this->options);}
				else {$this->options['ic_aya']-=$this->options['ic_ayat_count'];update_option("ic_options",$this->options);}
				
				$parsed_options=$this->GetDefaultOptions();
				$verse_text=file_get_contents($parsed_options['current_url']);
				$verse_text_str=$this->FormatVerseDataForWPDashboard($verse_text,$parsed_options);
				echo $verse_text_str;
				exit;
			}
	}
	
}
?>