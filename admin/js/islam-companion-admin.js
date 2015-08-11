/**
	 * Gets the text selected by the user	 
	 *
	 * @since    1.0.3
*/
function GetSelectionText()
	{
		var text = "";
		if (window.getSelection)text = window.getSelection().toString();
		else if (document.selection && document.selection.type != "Control")text = document.selection.createRange().text;		
		return text;
	}
		
/**
	 * Opens the dictionary url so user can check definition of word
	 *
	 * @since    1.0.3
*/		
function OpenDictionaryURL(dictionary_url)
	{
		var selected_text=GetSelectionText();
		if(selected_text=="")alert(objectL10n.selected_text_alert);
		else
			{
				dictionary_url=dictionary_url.replace("{word}",selected_text);
				window.open(dictionary_url);
			}
	}
	
/**
	 * Validates the settings form	 
	 *
	 * @since    1.0.3
*/	
function ValidateICSettingsForm()
	{
		var ic_language_str=document.getElementById('ic_language').value;
		var ic_narrator_str=document.getElementById('ic_narrator').value;			
		var ic_division_str=document.getElementById('ic_division').value;			
		
		if(ic_language_str==""){alert(objectL10n.language_alert);return false;}
		if(ic_narrator_str==""){alert(objectL10n.narrator_alert);return false;}
		if(ic_division_str==""){alert(objectL10n.division_alert);return false;}				
							
		return true;
	}
	
/**
	 * Displays the quranic divisions dropdowns 
	 *
	 * @since    1.2.0
*/
function DropdownUpdate(select_box_name)
	{
		var arguments={
			division_number: jQuery('#ic_division_number_box').val(),
			sura: jQuery('#ic_sura_box').val(),
			ruku: jQuery('#ic_ruku_number_box').val(),
			dropdown_box: select_box_name
		};
		
		var ajax_nonce=jQuery("#ic_ajax_nonce").val();
		var data={			
			action: "islam_companion",
			plugin_action: "dropdown_update",
			security: ajax_nonce,
			parameters:arguments,
			plugin: 'IC_HolyQuranDashboardWidget'
		}
		
		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		jQuery.post('admin-ajax.php', data, function(result) {
			var response=jQuery.parseJSON(result);						
			if(response.result=='success')jQuery("#ic-quran-dashboard-text").html(response.text);
			else alert(objectL10n.data_fetch_alert);
		});
	}
	
/**
	 * Fetches verse data from the server	 
	 *
	 * @since    1.0.3
*/
function FetchVerseData(ajax_nonce,direction)
	{		
		var data = {
					action: 'islam_companion',
					plugin_action: 'fetch_verse_data',
					security: ajax_nonce,
					plugin: 'IC_HolyQuranDashboardWidget',
					direction: direction
		};
		
		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		jQuery.post('admin-ajax.php', data, function(result) {
			var response=jQuery.parseJSON(result);						
			if(response.result=='success')jQuery("#ic-quran-dashboard-text").html(response.text);
			else alert(objectL10n.data_fetch_alert);
		});
	}
	

(function( jQuery ) {
	'use strict';

	/**
	 * All of the code for your Dashboard-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the jQuery function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * jQuery(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * jQuery( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

	 jQuery( window ).load(function() {		 
		 
		/**
		 * Handles language dropdown click event
		 *
		 * @since    1.0.0
		*/		 
		 jQuery("#ic_language").on("change",function(){
			 	jQuery('#ic_narrator').empty();
			 	var ic_language_str=jQuery("#ic_language").val();	  			
	  			var ic_narrator_hidden_str=jQuery("#ic_narrator_hidden").val();	  				  			
	  			var temp_arr=ic_narrator_hidden_str.split("@");
	  			
	  			if(ic_language_str!="")jQuery('#ic_narrator').append(jQuery('<option></option>').val("").html("--"+objectL10n.select_text+"--"));
	  			else jQuery('#ic_narrator').append(jQuery('<option></option>').val("").html("--"+objectL10n.language_select_text+"--"));
	  			for(var count=0;count<temp_arr.length;count++)
	  				{
		  				var temp_arr1=temp_arr[count].split("~");
		  				var temp_language=temp_arr1[0];
		  				var temp_narrator=temp_arr1[1];
		  				
		  				if(ic_language_str==temp_language)
		  					{			  					
								jQuery('#ic_narrator').append(jQuery('<option></option>').val(ic_language_str+"~"+temp_narrator).html(temp_narrator));											  			
		  					}
	  				}
		 });		 		
	 });
})( jQuery );