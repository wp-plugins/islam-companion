<?php

class IslamCompanionSettingsClass {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct($admin_object)
    {
        add_action( 'admin_menu', array( $this, 'create_settings_menu' ));
		add_action( 'admin_menu', array( $admin_object, 'create_custom_menu' ));
        add_action( 'admin_init', array( $this, 'page_init' ) );		
    }

    /**
     * Add options page
     */
    public function create_settings_menu($admin_object)
    {
        // This page will be under "Settings"
        add_options_page(
            'Islam Companion Settings', 
            'Islam Companion', 
            'manage_options', 
            'islam-companion-settings-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'ic_options' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Islam Companion Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'ic_option_group' );   
                do_settings_sections( 'islam-companion-settings-admin' );
                submit_button("Save Changes","primary","ic_submit",true,array("onclick"=>"return ValidateICSettingsForm();")); 
            ?>
            </form>
            <div>Powered By: <a href='http://tanzil.net/trans/' target='_new'>http://tanzil.net/trans/</a></div>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'ic_option_group', // Option group
            'ic_options', // Option name
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
            'Language', 
            array( $this, 'ic_language_callback' ), 
            'islam-companion-settings-admin', 
            'ic_settings_id'
        );      
        
        add_settings_field(
            'ic_narrator', 
            'Narrator', 
            array( $this, 'ic_narrator_callback' ), 
            'islam-companion-settings-admin', 
            'ic_settings_id'
        );
		
		add_settings_field(
            'ic_sura', 
            'Surah', 
            array( $this, 'ic_sura_callback' ), 
            'islam-companion-settings-admin', 
            'ic_settings_id'
        );     
		
		add_settings_field(
            'ic_aya', 
            'Aya', 
            array( $this, 'ic_aya_callback' ), 
            'islam-companion-settings-admin', 
            'ic_settings_id'
        );   
		
		add_settings_field(
            'ic_ayat_count', 
            'Ayat Count', 
            array( $this, 'ic_ayat_callback' ), 
            'islam-companion-settings-admin', 
            'ic_settings_id'
        );   
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['ic_language'] ) )
            $new_input['ic_language'] = sanitize_text_field( $input['ic_language'] );

        if( isset( $input['ic_narrator'] ) )
            $new_input['ic_narrator'] = sanitize_text_field( $input['ic_narrator'] );
            
		if( isset( $input['ic_sura'] ) )
            $new_input['ic_sura'] = sanitize_text_field( $input['ic_sura'] );
			
		if( isset( $input['ic_aya'] ) )
            $new_input['ic_aya'] = sanitize_text_field( $input['ic_aya'] );
			
		if( isset( $input['ic_ayat_count'] ) )
            $new_input['ic_ayat_count'] = sanitize_text_field( $input['ic_ayat_count'] );
		
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print '';
    }

	/** 
     * Displays ayat dropdown
     */
    public function ic_ayat_callback()
    {	    	
	    $current_ayat_count=$this->options['ic_ayat_count'];
	 	
	    $options="<option value=''>--Please Select--</option>";	   	

		for($count=1;$count<=10;$count++)
			{
				if($count==$current_ayat_count)$options.="<option value='".$count."' SELECTED>".$count."</option>\n";	   	
				else $options.="<option value='".$count."'>".$count."</option>\n";
			}
			
        printf(
		  '<select id="ic_ayat_count" name="ic_options[ic_ayat_count]" value="'.$current_ayat_count.'">'.$options.'</select>',
            isset( $this->options['ic_ayat_count'] ) ? esc_attr( $this->options['ic_ayat_count']) : ''
        );
    }
	
	/** 
     * Displays ayat dropdown
     */
    public function ic_aya_callback()
    {	    	
	    $current_ayat=$this->options['ic_aya'];
	 	$current_sura_ayat=$this->options['ic_sura'];
		list($current_sura,$ayas)=explode("~",$current_sura_ayat);
		
	    $options="<option value=''>--Please Select--</option>";	   	

		for($count=1;$count<=$ayas;$count++)
			{
				if($count==$current_ayat)$options.="<option value='".$count."' SELECTED>".$count."</option>\n";	   	
				else $options.="<option value='".$count."'>".$count."</option>\n";
			}
			
        printf(
		  '<select id="ic_aya" name="ic_options[ic_aya]" value="'.$current_ayat.'">'.$options.'</select>',
            isset( $this->options['ic_aya'] ) ? esc_attr( $this->options['ic_aya']) : ''
        );
    }

    /** 
     * Displays language dropdown
     */
    public function ic_language_callback()
    {	    	
		$encryption = new Encryption();
		
	    $current_language=$this->options['ic_language'];

	    $distinct_languages=file_get_contents("http://nadirlatif.me/scripts/api.php?option=".urlencode(base64_encode("get_distinct_languages")));	
		$distinct_languages=trim($encryption->DecryptText($distinct_languages));	
		$distinct_languages=json_decode($distinct_languages,true);
	 
	    $options='<option value="">--Please Select--</option>';
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
		  '<select id="ic_language" name="ic_options[ic_language]" value="'.$current_language.'">'.$options.'</select>',
            isset( $this->options['ic_language'] ) ? esc_attr( $this->options['ic_language']) : ''
        );
    }
    
    /** 
     * Displays Surah dropdown
     */
    public function ic_sura_callback()
    {	    	
		$encryption = new Encryption();
		
	    $current_sura_ayat=$this->options['ic_sura'];

	    $sura_names=file_get_contents("http://nadirlatif.me/scripts/api.php?option=".urlencode(base64_encode("get_sura_names")));	
		$sura_names=trim($encryption->DecryptText($sura_names));	
		$sura_names=json_decode($sura_names,true);
	 
	    $options='<option value="">--Please Select--</option>';
	   	for($count=0;$count<count($sura_names);$count++)
	   		{
		   		$sura=$sura_names[$count]['sura'];
				$ayas=$sura_names[$count]['ayas'];
				$temp_sura_ayat=addslashes($sura."~".$ayas);
		   		if($sura!="")
		   			{
			   			if($current_sura_ayat==($temp_sura_ayat))$options.='<option value="'.($temp_sura_ayat).'" SELECTED>'.$sura.'</option>'."\n";
			   			else $options.='<option value="'.($temp_sura_ayat).'">'.$sura.'</option>'."\n";
		   			}
	   		}

        printf(
		  '<select id="ic_sura" name="ic_options[ic_sura]" value="'.$current_sura.'">'.$options.'</select>',
            isset( $this->options['ic_sura'] ) ? esc_attr( $this->options['ic_sura']) : ''
        );
    }

   /** 
     * Displays narrator dropdown
     */
    public function ic_narrator_callback()
    {	    
		$encryption = new Encryption();
		
	    $current_language=$this->options['ic_language'];

	    $languages_narrator=file_get_contents("http://nadirlatif.me/scripts/api.php?option=".urlencode(base64_encode("get_distinct_languages_translators")));
		$languages_narrator=trim($encryption->DecryptText($languages_narrator));
		$languages_narrator=json_decode($languages_narrator,true);
	    	
	    list($current_langauge,$current_narrator)=explode("~",$this->options['ic_narrator']);
	   
	    if($current_langauge=="")$options_str='<option value="">--Please select a language first--</option>'."\n";
	    else $options_str='<option value="">--Please select--</option>'."\n";
	    
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
		  '<input type="hidden" name="ic_narrator_hidden" id="ic_narrator_hidden" value="'.$hidden_field_value.'" />'."\n".'<select id="ic_narrator" name="ic_options[ic_narrator]" value="'.$current_narrator.'">'.$options_str.'</select>',
            isset( $this->options['ic_narrator'] ) ? esc_attr( $this->options['ic_narrator']) : ''
        );
    }
}
