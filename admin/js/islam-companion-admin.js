/**
	 * Checks if settings form contains valid data
	 *
	 * @since    1.0.0
*/

function GetSelectionText()
	{
		var text = "";
		if (window.getSelection)text = window.getSelection().toString();
		else if (document.selection && document.selection.type != "Control")text = document.selection.createRange().text;		
		return text;
	}
		
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
	
function ValidateICSettingsForm()
	{
		var ic_language_str=document.getElementById('ic_language').value;
		var ic_narrator_str=document.getElementById('ic_narrator').value;
		var ic_sura_str=document.getElementById('ic_sura').value;
		var ic_ruku_str=document.getElementById('ic_ruku').value;
		
		if(ic_language_str==""){alert(objectL10n.language_alert);return false;}
		if(ic_narrator_str==""){alert(objectL10n.narrator_alert);return false;}
		if(ic_sura_str==""){alert(objectL10n.sura_alert);return false;}
		if(ic_ruku_str==""){alert(objectL10n.ruku_alert);return false;}
		return true;
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
		jQuery.post('admin-ajax.php', data, function(response) {
			var result=jQuery.parseJSON(response);						
			if(result.result=='success')jQuery("#ic-quran-dashboard-text").html(result.text);
			else alert(objectL10n.data_fetch_alert);
		});
	}
	
(function( $ ) {
	'use strict';

	/**
	 * All of the code for your Dashboard-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

	 $( window ).load(function() {		 
		 
		/**
		 * Handles language dropdown click event
		 *
		 * @since    1.0.0
		*/		 
		 $("#ic_language").on("change",function(){
			 	$('#ic_narrator').empty();
			 	var ic_language_str=$("#ic_language").val();	  			
	  			var ic_narrator_hidden_str=$("#ic_narrator_hidden").val();	  				  			
	  			var temp_arr=ic_narrator_hidden_str.split("@");
	  			
	  			if(ic_language_str!="")$('#ic_narrator').append($('<option></option>').val("").html("--"+objectL10n.select_text+"--"));
	  			else $('#ic_narrator').append($('<option></option>').val("").html("--"+objectL10n.language_select_text+"--"));
	  			for(var count=0;count<temp_arr.length;count++)
	  				{
		  				var temp_arr1=temp_arr[count].split("~");
		  				var temp_language=temp_arr1[0];
		  				var temp_narrator=temp_arr1[1];
		  				
		  				if(ic_language_str==temp_language)
		  					{			  					
								$('#ic_narrator').append($('<option></option>').val(ic_language_str+"~"+temp_narrator).html(temp_narrator));											  			
		  					}
	  				}
		 });
		 
		 
		 /**
		 * Handles sura dropdown click event
		 *
		 * @since    1.0.2
		*/	
		 $("#ic_sura").on("change",function(){
			 	$('#ic_ruku').empty();
			 	var ic_sura_str=$("#ic_sura").val();	  			
			 	ic_sura_str=ic_sura_str.replace('\\','');	  			
	  			var temp_arr=ic_sura_str.split("~");
	  			var rukus=parseInt(temp_arr[2]);
	  			
	  			if(ic_sura_str!="")$('#ic_ruku').append($('<option></option>').val("").html("--"+objectL10n.select_text+"--"));
	  			else $('#ic_sura').append($('<option></option>').val("").html("--"+objectL10n.sura_select_text+"--"));
	  			for(var count=1;count<=rukus;count++)
	  				{
		  				$('#ic_ruku').append($('<option></option>').val(count).html(count));		  				
	  				}
		 });
	 });
})( jQuery );