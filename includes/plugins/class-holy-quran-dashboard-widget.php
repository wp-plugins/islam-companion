<?php

/**
 * The file that defines the "Holy Quran Dashboard Widget" feature
 *
 * A class definition that includes attributes and functions used on the admin dashboard.
 *
 * @link:       http://pakjiddat.com
 * @since      1.0.0
 *
 * @package    Islam_Companion
 * @subpackage Islam_Companion/includes
 */

/**
 * The class for "Holy Quran Dashboard Widget" feature.
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
 * @author:       Nadir Latif <nadir@pakjiddat.com>
 */
class IC_HolyQuranDashboardWidget {
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
	}

	/**
	 * Returns a random Quranic verse from the Holy Quran for the given language and translator
	 *
	 * @since    1.0.0
	 */
	private function DisplayVerseText() {
		
		try	
			{								
				global $wp_meta_boxes;
		
				$user_id=get_current_user_id();
				$this->options = get_option( 'ic_options_'.$user_id );
					
				wp_add_dashboard_widget('holy-quran-dashboard-widget', __('Holy Quran','islam-companion'), array($this,'CustomDashBoardText'));
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
	
	/**
	 * Gets the default options configured by the user
	 *
	 * @since    1.0.3
	 * @return   array    The list of options configured by the user
	 */
	private function GetDefaultOptions() {
		
		try
			{
				list($current_language,$current_narrator)=explode("~",$this->options['ic_narrator']);
			    $current_language=$this->options['ic_language'];
				$current_ruku=$this->options['ic_ruku'];
				$current_division=$this->options['ic_division'];
				$current_division_number=$this->options['ic_division_number'];
				$current_ayat=$this->options['ic_ayat'];
				$current_ayat_count=$this->options['ic_ayat_count'];
								
				list($current_sura,$total_ayat_count,$total_rakaat_count)=explode("~",$this->options['ic_sura']);
			
				$this->options['ic_division_number']=$current_division_number."-".$current_sura."-".$current_ayat;
				$current_division_number=$this->options['ic_division_number'];
				
				$current_url=API_URL."?option=".urlencode(base64_encode("get_sura_verses")).
						"&lang=".urlencode(base64_encode($current_language)).
						"&narrator=".urlencode(base64_encode($current_narrator)).
						"&sura=".urlencode(base64_encode($current_sura)).
						"&division=".urlencode(base64_encode($current_division)).
						"&division_number=".urlencode(base64_encode($current_division_number)).
						"&ayat=".urlencode(base64_encode($current_ayat)).						
						"&ruku=".urlencode(base64_encode($current_ruku));
					
				$division_url=API_URL."?option=".urlencode(base64_encode("get_division_meta")).
				"&division=".urlencode(base64_encode($current_division)).
				"&division_number=".urlencode(base64_encode($current_division_number));
				
				$parsed_options=array("total_rakaat_count"=>$total_rakaat_count,"current_language"=>$current_language,
				"current_narrator"=>$current_narrator,"current_sura"=>$current_sura,"current_ruku"=>$current_ruku,
				"current_url"=>$current_url,"division_url"=>$division_url,"current_division"=>$current_division,
				"current_division_number"=>$current_division_number,"current_ayat"=>$current_ayat,
				"current_ayat_count"=>$current_ayat_count);
				
				return $parsed_options;
			} 
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
	
	/**
	 * Returns a random Quranic verse from the Holy Quran for the given language and translator
	 *
	 * @since    1.0.0
	 */
	public function CustomDashBoardText( $post, $callback_args ) {				
		
		try
			{
				$parsed_options=$this->GetDefaultOptions();
			
				$verse_text=file_get_contents($parsed_options['current_url']);
				$verse_text_str=$this->FormatVerseDataForWPDashboard($verse_text,$parsed_options);
								
				echo $verse_text_str;
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
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
			try
				{					
					$encryption = new Encryption();
					
					$verse_text=$encryption->DecryptText($verse_text);
					$verse_information=json_decode( trim($verse_text), true);
				
					if(json_last_error()!=JSON_ERROR_NONE||$verse_information['result']!="success")throw new Exception("Error in Islam Companion Plugin");

					$start_ayat=$verse_information['start_ayat'];
					$end_ayat=$verse_information['end_ayat'];
					$sura_ename=$verse_information['sura_ename'];
					$verse_text=$verse_information['text'];
					$audio_filename=$verse_information['audiofile_name'];
					
					$division_information=$verse_information['division_information'];
					$user_id=get_current_user_id();
					$_SESSION['ic_next_navigation_'.$user_id]=$division_information['next_navigation'];
					$_SESSION['ic_prev_navigation_'.$user_id]=$division_information['prev_navigation'];					
					
					$language_information=file_get_contents(API_URL."?option=".urlencode(base64_encode("get_language_information"))."&lang=".urlencode(base64_encode($parsed_options['current_language'])));					
					$language_information=json_decode(trim($encryption->DecryptText(($language_information))),true);
										
					if($language_information['result']!="success")throw new Exception("Error in Islam Companion Plugin");
					
					$is_language_rtl=$language_information['text']['rtl'];
					
					list($current_division_number,$surah_number,$ayat_number)=explode("-",$parsed_options['current_division_number']);
										
					$online_dictionary_url=$language_information['text']['dictionary_url'];
					//$online_dictionary_url="http://urdulughat.info/words/advance-search/name:{word}";
					$css_class=$language_information['text']['css_class'];
							
					$rtl=($is_language_rtl=='true')?'right':'left';
					$direction=($is_language_rtl=='true')?'rtl':'ltr';
					$navigator_direction=($is_language_rtl=='true')?'left':'right';
					$meta_information_class='ic-sura-heading';
					$navigator_class='ic-navigator-class';
					$dashboard_text_class=($is_language_rtl=='true')?'ic-rtl-dashboard-text':'ic-ltr-dashboard-text';
					if($online_dictionary_url!="")$online_dictionary_link="<img id='ic-online-dictionary-link' src='/wp-content/plugins/islam-companion/admin/images/dictionary.png' alt='".__("Dictionary","islam-companion")."' title='".__("Dictionary","islam-companion")."' onclick='OpenDictionaryURL(\"".$online_dictionary_url."\");'/>";
					else $online_dictionary_link="";
					
					$ajax_nonce = wp_create_nonce("islam-companion");			
					$ayat_text_str="";
					$ayat_text_arr=explode("~",$verse_text);
					$next_link_text=($is_language_rtl=='false')?__("next","islam-companion")." &raquo; ":"&laquo; ".__("next","islam-companion");
					$previous_link_text=($is_language_rtl=='false')?"&laquo; ".__("prev","islam-companion"):__("prev","islam-companion")." &raquo;";
					$navigation_links=$previous_link=$next_link=$separator="";
					
					$previous_link="<b><button class='button button-primary' onclick='FetchVerseData(\"".$ajax_nonce."\",\"prev\");'>".$previous_link_text."</button>";			 									
					$next_link="<button class='button button-primary' onclick='FetchVerseData(\"".$ajax_nonce."\",\"next\");'>".$next_link_text."</button>";										
					$separator="&nbsp;&nbsp;";
					
					if($is_language_rtl=='false')$navigation_links="<div class='".$navigator_class."'>".$previous_link.$separator.$next_link."</div>";
					else $navigation_links="<div class='".$navigator_class."'>".$next_link.$separator.$previous_link."</div>";
					
					for($count=0;$count<count($ayat_text_arr);$count++)$ayat_text_str.="<li>".$ayat_text_arr[$count]."</li>";	
		
					$ayat_text_str="<ol start='".$start_ayat."' class='".$css_class."'>".$ayat_text_str."</ol>";	
					
					$verse_text_str="";
					
					$verse_text_str.="<div id='ic-quran-dashboard-text' class='".$dashboard_text_class."'>";
					
					$audio_file_ruku=($parsed_options['current_ruku']<10)?"rukoo0".$parsed_options['current_ruku']:"rukoo".$parsed_options['current_ruku'];									
					
					$verse_text_str.="<span class='".$meta_information_class."'><b>";					
					if($parsed_options['current_division']!='sura')$verse_text_str.=ucfirst($parsed_options['current_division'])." ".$current_division_number.", ";						
					$verse_text_str.=__("Sura","islam-companion")." ".$parsed_options['current_sura']." (".$sura_ename."), ";
					$verse_text_str.=sprintf("%s %d %s %d",__("Ruku","islam-companion"),$parsed_options['current_ruku'],__("of","islam-companion"),$parsed_options['total_rakaat_count']);
					$verse_text_str.=sprintf(", %s %d-%d",__("Aya","islam-companion"),$start_ayat,$end_ayat)."</b></span><br/><hr/>";
						
					$verse_text_str.="<div class='section-padding'>";
					if($parsed_options['current_language']=="Urdu"||$parsed_options['current_language']=="Arabic")$verse_text_str.="<audio controls class='ic-audio-control'><source src='http://res.cloudinary.com/web-innovation/raw/upload/".$audio_file_ruku.$audio_filename.".mp3' type='audio/mpeg'>".__("Your browser does not support the audio element")."</audio>";
					$verse_text_str.=$online_dictionary_link;
					$verse_text_str.=$navigation_links."</div><hr/>";
				
					$verse_text_str.=$ayat_text_str."</div>";
					
					return $verse_text_str;
				}
			catch(Exception $e)
				{
					throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
				}
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
	 * Function that is used to update the version meta information
	 *
	 * @since    1.0.8
	 * @var      string    $action       The action chosen by the users. it can be next or prev.	 	 
	 */
	public function UpdateOptions($action) {		
			
		$user_id=get_current_user_id();
		
		$parsed_options=$this->GetDefaultOptions();
		list($current_sura,$total_ayat_count,$total_rakaat_count)=explode("~",$this->options['ic_sura']);

		if($action=="next")$navigation_options=$_SESSION['ic_next_navigation_'.$user_id];
		else $navigation_options=$_SESSION['ic_prev_navigation_'.$user_id];
					
		$this->options['ic_sura']=$navigation_options['sura'];
		$this->options['ic_division_number']=$navigation_options['division_number']."-".$navigation_options['sindex']."-".$navigation_options['ayat'];
		$this->options['ic_ayat']=$navigation_options['ayat'];
		$this->options['ic_ruku']=$navigation_options['sura_ruku'];												
		
		update_option("ic_options_".$user_id,$this->options);		
	}
	
	/**
	 * Function that is used to add a sub menu
	 *
	 * @since    1.0.3
	 */
	public function IslamCompanionAjax() {		
			
		try
			{
				$plugin_action=$_POST['plugin_action'];		
				$user_id=get_current_user_id();
				$this->options = get_option( 'ic_options_'.$user_id );
						
				if($plugin_action=='fetch_verse_data')
					{
						$action=$_POST['direction'];
						$this->UpdateOptions($action);												
					
						$parsed_options=$this->GetDefaultOptions();
						$verse_text=file_get_contents($parsed_options['current_url']);
						
						$verse_text_str=$this->FormatVerseDataForWPDashboard($verse_text,$parsed_options);
						
						echo json_encode(array("result"=>"success","text"=>$verse_text_str));
						exit;
					}
				if($plugin_action=='fetch_division_data')
					{
						$encryption = new Encryption();
						
						$division=$_POST['division'];
						$division_number=$_POST['division_number'];
						
						$this->options['ic_division']=$division;
						$this->options['ic_division_number']=$division_number;
																					
						$parsed_options=$this->GetDefaultOptions();
						$division_data=file_get_contents($parsed_options['division_url']);						
						$division_data=$encryption->DecryptText($division_data);
						$division_data=json_decode( trim($division_data), true);
					
						echo json_encode(array("result"=>"success","text"=>$division_data['text']));
						exit;
					}
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
	
}
?>