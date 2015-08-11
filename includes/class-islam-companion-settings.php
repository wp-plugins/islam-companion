<?php

class IslamCompanionSettingsClass {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
	 /**
     * Holds the site settings. e.g user id of current user
     */
    public $site_settings;
    /**
     * Start up
     */
    public function __construct($admin_object)
    {
    	try
    		{
        		add_action( 'admin_menu', array( $this, 'create_settings_menu' ));
				add_action( 'admin_menu', array( $admin_object, 'create_custom_menu' ));
        		add_action( 'admin_init', array( $this, 'page_init' ) );
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}	
    }

   /**
	 * Add options page
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    public function create_settings_menu($admin_object)
    {
    	
		try
			{
		        // This page will be under "Settings"
		        add_options_page(
		            __('Islam Companion Settings','islam-companion'), 
		            __('Islam Companion','islam-companion'), 
		            'manage_options', 
		            'islam-companion-settings-admin', 
		            array( $this, 'create_admin_page' )
		        );
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
    }

    /**
	 * Options page callback
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    public function create_admin_page()
    {
    		try
	    		{
			        // Set class property
			        $this->options = get_option( 'ic_options_'.$this->site_settings['user_id'] );
					
					$dropdown_values=json_encode(array(
								"division"=>$this->options['ic_division'],
								"division_number"=>$this->options['ic_division_number'],
								"ayat"=>$this->options['ic_ayat'],
								"sura"=>$this->options['ic_sura'],
					));
					
			        ?>
			        <div class="wrap">
			            <?php screen_icon(); ?>
			            <h2><?php _e("Islam Companion Settings","islam-companion");?></h2>           
			            <form method="post" action="options.php" id="ic_form">
			            <input type="hidden" name="ic_dropdown_values" id="ic_dropdown_values" value='<?php echo $dropdown_values;?>'/>			
			            <input type="hidden" name="ic_ajax_nonce" id="ic_ajax_nonce" value="<?php echo wp_create_nonce("islam-companion");?>"/>            
			            <?php
			                // This prints out all hidden setting fields
			                settings_fields( 'ic_option_group' );   
			                do_settings_sections( 'islam-companion-settings-admin' );
			                submit_button(__("Save Changes","islam-companion"),"primary","ic_submit",true,array("onclick"=>"return ValidateICSettingsForm();")); 
			            ?>
			            </form>
			            <div><?php _e("Holy Quran Divisions","islam-companion");?>: 
			            	<a href='https://en.wikipedia.org/wiki/Rub_el_Hizb' target='_new'>Hizb</a> | 
			            	<a href='https://en.wikipedia.org/wiki/Juz%27' target='_new'>Juz</a> | 
			            	<a href='https://en.wikipedia.org/wiki/Manzil' target='_new'>Manzil</a>
			            </div><br/>
			            <div><?php _e("Powered By","islam-companion");?>: <a href='http://tanzil.net/trans/' target='_new'>http://tanzil.net/trans/</a></div>
			            <div><?php _e("Report a bug","islam-companion");?>: <a href='https://wordpress.org/support/plugin/islam-companion' target='_new'>https://wordpress.org/support/plugin/islam-companion</a></div>
			            <div><?php _e("Suggest a feature","islam-companion");?>: <a href='https://wordpress.org/support/plugin/islam-companion' target='_new'>https://wordpress.org/support/plugin/islam-companion</a></div>
			        </div>
			        <?php
		       	}
			catch(Exception $e)
				{
					throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
				}
    }

    /**
	 * Register and add settings
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    public function page_init()
    {
    	try
    		{
    			$this->SetDefaultOptions();
    			$user_id=$this->site_settings['user_id'];
				$ic_division=$ic_division=$this->options['ic_division'];			
				
		        register_setting(
		            'ic_option_group', // Option group
		            'ic_options_'.$user_id, // Option name
		            array( $this, 'sanitize' ) // Sanitize
		        );
		
		        add_settings_section(
		            'ic_settings_id', // ID
		            '', // Title
		            array( $this, 'print_section_info' ), // Callback
		            'islam-companion-settings-admin' // Page
		        );    
		
		        add_settings_field(
		            'ic_language', 
		            __('Language','islam-companion'), 
		            array( $this, 'ic_language_callback' ), 
		            'islam-companion-settings-admin', 
		            'ic_settings_id'
		        );      
		        
		        add_settings_field(
		            'ic_narrator', 
		            __('Narrator','islam-companion'), 
		            array( $this, 'ic_narrator_callback' ), 
		            'islam-companion-settings-admin', 
		            'ic_settings_id'
		        );
				
				add_settings_field(
		            'ic_division', 
		            __('Division','islam-companion'), 
		            array( $this, 'ic_division_callback' ), 
		            'islam-companion-settings-admin', 
		            'ic_settings_id'
		        );		
		 	}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
    }

	/**
	 * Sets default options if no options were set by the user
	 *
	 * @since    1.0.2
	 */
	private function SetDefaultOptions() {
		
		try
			{
				session_start();
				$this->site_settings=array("user_id"=>get_current_user_id());
				$user_id=$this->site_settings['user_id'];

				delete_option("ic_options");
				$this->options = get_option( 'ic_options_'.$user_id );
				
				if(!isset($this->options['ic_narrator'])||(isset($this->options['ic_narrator'])&&$this->options['ic_narrator']=='')
				||(isset($this->options['ic_narrator'])&&strpos($this->options['ic_narrator'],"~")===false))$this->options['ic_narrator']="English~Mohammed Marmaduke William Pickthall";
				if(!isset($this->options['ic_language'])||(isset($this->options['ic_language'])&&$this->options['ic_language']==''))$this->options['ic_language']="English";
				if(!isset($this->options['ic_sura'])||(isset($this->options['ic_sura'])&&$this->options['ic_sura']==''))$this->options['ic_sura']="Al-Faatiha~7~1";	
				if(!isset($this->options['ic_ruku'])||(isset($this->options['ic_ruku'])&&$this->options['ic_ruku']==''))$this->options['ic_ruku']="1";
				if(!isset($this->options['ic_division'])||(isset($this->options['ic_division'])&&$this->options['ic_division']==''))$this->options['ic_division']="sura";
				if(!isset($this->options['ic_division_number'])||(isset($this->options['ic_division_number'])&&$this->options['ic_division_number']==''))$this->options['ic_division_number']="1";
				if(!isset($this->options['ic_ayat'])||(isset($this->options['ic_ayat'])&&$this->options['ic_ayat']==''))$this->options['ic_ayat']="1";				
				
				update_option("ic_options_".$user_id,$this->options);
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
    	try
    		{
		        $new_input = array();
		
		        if( isset( $input['ic_language'] ) )
		            $new_input['ic_language'] = sanitize_text_field( $input['ic_language'] );
		
		        if( isset( $input['ic_narrator'] ) )
		            $new_input['ic_narrator'] = sanitize_text_field( $input['ic_narrator'] );	
				
				if( isset( $input['ic_division'] ) )
		            $new_input['ic_division'] = sanitize_text_field( $input['ic_division'] );
				
		        return $new_input;
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			} 
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print '';
    }
	
	/** 
     * Displays division dropdown
     */
    public function ic_division_callback()
    {
    	try
    		{
    			$user_id=$this->site_settings['user_id'];
			    $current_ruku=$this->options['ic_ruku'];
			 	$current_sura_ayat_rakaat=$this->options['ic_sura'];
				$current_division=$this->options['ic_division'];
				list($current_sura,$ayas,$rukus)=explode("~",$current_sura_ayat_rakaat);
				
				$divisions_arr=array("--".__("Please Select","islam-companion")."--","sura","hizb","juz","manzil","page");
				
				for($count=0;$count<count($divisions_arr);$count++)
					{
						$division=$divisions_arr[$count];
						if($count==0)$division_value="";
						else $division_value=$division;
						if($current_division==$division)$options.="<option value='".$division_value."' SELECTED>".ucfirst($division)."</option>\n";
						else $options.="<option value='".$division_value."'>".ucfirst($division)."</option>\n";
					}

		        printf('<select id="ic_division" name="ic_options_'.$user_id.'[ic_division]">%s</select>',$options);
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
    }

    /** 
     * Displays language dropdown
     */
    public function ic_language_callback()
    {
    	try
    		{	    	
				$encryption = new Encryption();
				
				$user_id=$this->site_settings['user_id'];
			    $current_language=$this->options['ic_language'];
		
			    $response=file_get_contents(API_URL."?option=".urlencode(base64_encode("get_distinct_languages")));
					
				$response=trim($encryption->DecryptText($response));
				$response=json_decode(trim($response),true);
			 
			 	if($response['result']!='success')throw new Exception("Error in Islam Companion Plugin. Details: ".$response['text']);
				else $distinct_languages=$response['text'];
			 
			    $options='<option value="">--'.__("Please Select","islam-companion").'--</option>';
			   	for($count=0;$count<count($distinct_languages);$count++)
			   		{
				   		$language=$distinct_languages[$count]['language'];
				   		if($language!="")
				   			{
					   			if($current_language==$language)$options.='<option value="'.($language).'" SELECTED>'.$language.'</option>'."\n";
					   			else $options.='<option value="'.($language).'">'.$language.'</option>'."\n";
				   			}
			   		}
		
		        printf(
				  '<select id="ic_language" name="ic_options_'.$user_id.'[ic_language]" value="%s">%s</select>',
		            isset( $this->options['ic_language'] ) ? esc_attr( $this->options['ic_language']) : '',
		            $options
		        );
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
    }
       
   /** 
     * Displays narrator dropdown
     */
    public function ic_narrator_callback()
    {
	    	try
	    	{
	    		$user_id=$this->site_settings['user_id'];
					    
				$encryption = new Encryption();
				
			    $current_language=$this->options['ic_language'];
		
			    $response=file_get_contents(API_URL."?option=".urlencode(base64_encode("get_distinct_languages_translators")));
				$response=$encryption->DecryptText($response);
				$response=json_decode(trim($response),true);
		
				if($response['result']!='success')throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
				else $languages_narrator=$response['text'];
				
			    list($current_langauge,$current_narrator)=explode("~",$this->options['ic_narrator']);
			   
			    if($current_langauge=="")$options_str='<option value="">--'.__("Please select a language first","islam-companion").'--</option>'."\n";
			    else $options_str='<option value="">--'.__("Please select","islam-companion").'--</option>'."\n";
			  
			    $options_arr=array();
			   	for($count=0;$count<count($languages_narrator);$count++)
			   		{
				   		$language=(utf8_decode($languages_narrator[$count]['language']));
				   		$narrator=(utf8_decode($languages_narrator[$count]['translator']));			
				   		if($language!="")
				   			{
					   			$options_arr[]=$language."~".$narrator;
					   			if($current_langauge==$language)
					   				{								
						   				if($current_narrator==$narrator)$options_str.='<option value="'.$language."~".$narrator.'" SELECTED>'.$narrator.'</option>';
						   				else $options_str.='<option value="'.$language."~".$narrator.'">'.$narrator.'</option>';
					   				}
				   			}
			   		}
			   		
			   	$hidden_field_value=trim(implode("@",$options_arr),"~");
			   		
		        printf(
				  '<input type="hidden" name="ic_narrator_hidden" id="ic_narrator_hidden" value="%s" />'."\n".'<select id="ic_narrator" name="ic_options_'.$user_id.'[ic_narrator]" value="%s">%s</select>',
				    $hidden_field_value,
		            isset( $this->options['ic_narrator'] ) ? esc_attr( $this->options['ic_narrator']) : '',
		            $options_str
		        );
		     }    	
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
}
