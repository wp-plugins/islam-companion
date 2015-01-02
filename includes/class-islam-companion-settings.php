<?php

class IslamCompanionSettingsClass {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
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
     * Displays language dropdown
     */
    public function ic_language_callback()
    {	    
	    global $wpdb;
	    $current_language=$this->options['ic_language'];
	    $rows = $wpdb->get_results( "SELECT DISTINCT language FROM qa_quranic_text_meta" );
	    
	    $options="<option value=''>--Please Select--</option>";
	   	for($count=0;$count<count($rows);$count++)
	   		{
		   		$language=$rows[$count]->language;
		   		if($language!="")
		   			{
			   			if($current_language==$language)$options.="<option value='".($language)."' SELECTED>".$language."</option>\n";
			   			else $options.="<option value='".($language)."'>".$language."</option>\n";
		   			}
	   		}

        printf(
		  '<select id="ic_language" name="ic_options[ic_language]" id="ic_language" value="'.$current_language.'">'.$options.'</select>',
            isset( $this->options['ic_language'] ) ? esc_attr( $this->options['ic_language']) : ''
        );
    }
    
   /** 
     * Displays narrator dropdown
     */
    public function ic_narrator_callback()
    {
	    global $wpdb;
	    	   
	    list($current_langauge,$current_narrator)=explode("~",$this->options['ic_narrator']);
	    $rows = $wpdb->get_results( "SELECT language,translator FROM qa_quranic_text_meta GROUP BY translator" );

	    if($current_langauge=="")$options_str='<option value="">--Please select a language first--</option>'."\n";
	    else $options_str='<option value="">--Please select--</option>'."\n";
	    
	    $options_arr=array();
	   	for($count=0;$count<count($rows);$count++)
	   		{
		   		$language=(utf8_decode($rows[$count]->language));
		   		$narrator=(utf8_decode($rows[$count]->translator));
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
