/**
	 * Checks if settings form contains valid data
	 *
	 * @since    1.0.0
*/

var ic_division_meta_data=Array();

/**
	 * Shows/Hides the form dropdowns
	 *
	 * @since    1.0.8
*/
function ShowHideFormDropdowns()
	{
		var current_division=jQuery('#ic_division').val();
		if(current_division=="sura")
			 {
			 	jQuery('#ic_division_number').parent().parent().hide();
			  	jQuery('#ic_ayat').parent().parent().hide();	  			
	  			jQuery('#ic_ruku').parent().parent().show();		
			 }
		else
			{
				jQuery('#ic_division_number').parent().parent().show();
			  	jQuery('#ic_ayat').parent().parent().show();	  			
	  			jQuery('#ic_ruku').parent().parent().hide();	
			}		
	}

/**
	 * Displays the ayat dropdown 
	 *
	 * @since    1.0.8
*/
function DisplayAyatDropDown()
	{			  
		var ic_dropdown_values_str=jQuery.parseJSON(jQuery("#ic_dropdown_values").val());		
		var ic_division_number=jQuery("#ic_division_number").val()
		
		if(ic_division_number!='')
			{
				var temp_arr=ic_division_number.split("-");
				ic_ayat=temp_arr[2];
			}		
		else ic_ayat=ic_dropdown_values_str['ayat'];
		
	  	var options_text='<option value="">--'+objectL10n.sura_select_text+'--</option>'+"\n";	  	
	  	jQuery('#ic_ayat').html(options_text);
	  
	  	if(jQuery('#ic_sura').val()==undefined||jQuery('#ic_division').val()=="sura")return;
			
		var options_text='<option value="">--'+objectL10n.select_text+'--</option>'+"\n";	  	
		
	  	var tmp_sura_arr=jQuery('#ic_sura').val().split("~");
	  	var tmp_sura_name=tmp_sura_arr[0]*1;  		
	  	var tmp_sura_max_ayat=tmp_sura_arr[1]*1;
	  	var tmp_sura_max_ruku=tmp_sura_arr[2]*1;
	  		
	  	for(count=1;count<=tmp_sura_max_ayat;count++)
	  		{
	  			if(count==ic_ayat)options_text+='<option value="'+count+'" SELECTED>'+count+'</option>'+"\n";	  		  			
	  			else options_text+='<option value="'+count+'">'+count+'</option>'+"\n";
	  		}
	  	jQuery('#ic_ayat').html(options_text);
	}
	
/**
	 * Displays the sura dropdown 
	 *
	 * @since    1.0.8
*/
function DisplaySurahDropDown()
	{
		var options_text="";	  	
	  	
	  	if(jQuery('#ic_division').val()==undefined)options_text='<option value="">--'+objectL10n.division_select_text+'--</option>'+"\n";
	  	if(jQuery('#ic_division').val()!="sura"&&(jQuery('#ic_division_number').val()==undefined))options_text='<option value="">--'+objectL10n.division_number_select_text+'--</option>'+"\n";
	  	
	  	jQuery('#ic_sura').html(options_text);
	  	
	  	if(jQuery('#ic_division').val()==undefined)return;		
		if(jQuery('#ic_division').val()!="sura"&&jQuery('#ic_division_number').val()==undefined)return;
		
		var options_text='<option value="">--'+objectL10n.select_text+'--</option>'+"\n";	  	
		
		var options_text=ic_division_meta_data['sura'];
		jQuery('#ic_sura').html(options_text);
		
		if(jQuery('#ic_division').val()=="sura")return;
			
		var current_division_number_arr=jQuery('#ic_division_number').val().split("-");
  		var current_division_number=current_division_number_arr[0]*1;  		
  		var current_start_sura=current_division_number_arr[1]*1;
  		var current_start_ayat=current_division_number_arr[2]*1;
  		var current_end_sura="onwards";
	  	var current_end_ayat="onwards";

  		jQuery("#ic_division_number > option").each(function() {
		    var tmp_division_number_arr=this.value.split("-");
	  		var tmp_division_number=tmp_division_number_arr[0]*1;  		
	  		var tmp_sura=tmp_division_number_arr[1]*1;
	  		var tmp_start_ayat=tmp_division_number_arr[2]*1;	  		
	  		if(tmp_division_number==(current_division_number+1))
	  			{
	  				current_end_sura=tmp_division_number_arr[1]*1;
  					current_end_ayat=tmp_division_number_arr[2]*1;
	  			}
		});
			
		var count=0;
		var sura_arr=Array();
		var ic_dropdown_values_str=jQuery.parseJSON(jQuery("#ic_dropdown_values").val());		
		var ic_sura_str=ic_dropdown_values_str['sura'];
		
		jQuery("#ic_sura > option").each(function() {
			var is_hidden=false;
			count=this.id;
			if(count=="")
				{
					if(ic_sura_str==this.value)sura_arr.push("<option value='"+this.value+"' id='"+this.id+"' SELECTED>"+this.text+"</option>");
					else sura_arr.push("<option value='"+this.value+"' id='"+this.id+"'>"+this.text+"</option>");
					return;
				}
			else 
				{
					var temp_arr=count.split("-");
					count=temp_arr[1];
				}
		    var tmp_sura_arr=this.value.split("~");
	  		var tmp_sura_name=tmp_sura_arr[0]*1;  		
	  		var tmp_sura_max_ayat=tmp_sura_arr[1]*1;
	  		var tmp_sura_max_ruku=tmp_sura_arr[2]*1;
	  		if(count<current_start_sura)is_hidden=true;	  		
	  		if(current_end_sura!="onwards")
	  			{
	  				if(count>current_end_sura)is_hidden=true;		  				
	  				if(count==current_end_sura&&current_end_ayat==1)is_hidden=true;		  				
	  			}
	  		if(!is_hidden)
	  			{	  				
	  				if(ic_sura_str==this.value)sura_arr.push("<option value='"+this.value+"' id='"+this.id+"' SELECTED>"+this.text+"</option>");
					else sura_arr.push("<option value='"+this.value+"' id='"+this.id+"'>"+this.text+"</option>");
	  			}
	  		
	  		jQuery('#ic_sura').html(sura_arr.join("\n"));	  		
		});
	}
	
/**
	 * Displays ruku dropdown
	 *
	 * @since    1.0.8
*/
function DisplayRukuDropDown()
	{
		jQuery('#ic_ruku').empty();
		var ic_sura_str=jQuery("#ic_sura").val();	  			
		ic_sura_str=ic_sura_str.replace('\\','');	  			
	  	var temp_arr=ic_sura_str.split("~");
	  	var rukus=parseInt(temp_arr[2]);
	  	var ic_dropdown_values_str=jQuery.parseJSON(jQuery("#ic_dropdown_values").val());		
		var ic_ayat_str=ic_dropdown_values_str['ayat'];
		
	  	if(ic_sura_str!="")jQuery('#ic_ruku').append(jQuery('<option></option>').val(count).html("--"+objectL10n.select_text+"--"));	
	  	else jQuery('#ic_ruku').append(jQuery('<option></option>').val(count).html("--"+objectL10n.sura_select_text+"--"));	
	  	for(var count=1;count<=rukus;count++)
	  		{
		  		if(ic_ayat_str==count)jQuery('#ic_ruku').append(jQuery('<option SELECTED></option>').val(count).html(count));
		  		else jQuery('#ic_ruku').append(jQuery('<option></option>').val(count).html(count));
	  		}
	}
		
/**
	 * Displays the division number dropdown 
	 *
	 * @since    1.0.8
*/
function DisplayDivisionNumberDropDown()
	{
		var options_text='<option value="">--'+objectL10n.division_select_text+'--</option>'+"\n";	  	
	  		  	
	  	jQuery('#ic_division_number').html(options_text);
	  
	  	if(jQuery('#ic_division').val()==undefined||jQuery('#ic_division').val()==""||jQuery('#ic_division').val()=="sura")return;		
	  	
		options_text='<option value="">--'+objectL10n.select_text+'--</option>'+"\n"+options_text;
		
		jQuery('#ic_division_number').html(options_text);	
		
		var ic_division_str=jQuery("#ic_division").val();
		
		jQuery('#ic_ruku').parent().parent().hide();
		
		jQuery('#ic_division_number').empty();
			  					
		var options_text_arr=ic_division_meta_data[ic_division_str].split("@");				
		
		options_text='<option value="">--'+objectL10n.select_text+'--</option>'+"\n";
		
		jQuery('#ic_division_number').html(options_text);	
				
		for(count=0;count<options_text_arr.length;count++)
			{
				var options_str=options_text_arr[count];
				var temp_arr=options_str.split("~");
				
				jQuery('#ic_division_number').append(jQuery('<option '+temp_arr[2]+'></option>').val(temp_arr[0]).html(temp_arr[1]));

			}
	}

/**
	 * Displays the quranic divisions dropdowns 
	 *
	 * @since    1.0.8
*/
function DisplayQuranicDivisionsDropDowns()
	{
		DisplayDivisionNumberDropDown();
						
  		DisplaySurahDropDown();
  		
		DisplayAyatDropDown();
		
	}
	
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
		var ic_sura_str=document.getElementById('ic_sura').value;
		var ic_ruku_str=document.getElementById('ic_ruku').value;
		var ic_division_str=document.getElementById('ic_division').value;
		var ic_division_number_str=document.getElementById('ic_division_number').value;
		var ic_ayat_str=document.getElementById('ic_ayat').value;			
		
		if(ic_division_str!="sura")
			{
				if(ic_division_number_str==""){alert(objectL10n.division_number_alert);return false;}
				if(ic_ayat_str==""){alert(objectL10n.ayat_alert);return false;}
				if(ic_language_str==""){alert(objectL10n.language_alert);return false;}
				if(ic_narrator_str==""){alert(objectL10n.narrator_alert);return false;}
				if(ic_sura_str==""){alert(objectL10n.sura_alert);return false;}				
				if(ic_division_str==""){alert(objectL10n.division_alert);return false;}
			}
		else
			{
				if(ic_language_str==""){alert(objectL10n.language_alert);return false;}
				if(ic_narrator_str==""){alert(objectL10n.narrator_alert);return false;}
				if(ic_sura_str==""){alert(objectL10n.sura_alert);return false;}
				if(ic_ruku_str==""){alert(objectL10n.ruku_alert);return false;}
				if(ic_division_str==""){alert(objectL10n.division_alert);return false;}
			}
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
		jQuery.post('admin-ajax.php', data, function(result) {
			var response=jQuery.parseJSON(result);						
			if(response.result=='success')jQuery("#ic-quran-dashboard-text").html(response.text);
			else alert(objectL10n.data_fetch_alert);
		});
	}
	
function FetchDropdownData()
	{
		var ic_division_str=jQuery("#ic_division").val();
		var ic_dropdown_values_str=jQuery.parseJSON(jQuery("#ic_dropdown_values").val());
		var ajax_nonce=jQuery("#ic_ajax_nonce").val();
		var ic_division_number_str=ic_dropdown_values_str['division_number'];
		
		var data = {
						action: 'islam_companion',
						plugin_action: 'fetch_division_data',
						division: ic_division_str,
						division_number: ic_division_number_str,
						security: ajax_nonce,
						plugin: 'IC_HolyQuranDashboardWidget'		
					};
		// used to fetch meta data about quranic divisions from server
		jQuery.post('admin-ajax.php', data, function(result) {
			var response=jQuery.parseJSON(result);
			if(ic_division_meta_data['sura']==undefined)ic_division_meta_data['sura']=jQuery("#ic_sura").html();
			if(ic_division_str!="sura")ic_division_meta_data[ic_division_str]=response.text;
			if(response.result=='success')
				{
					DisplayQuranicDivisionsDropDowns();
					ShowHideFormDropdowns();
					jQuery('#ic_form').show();
				}
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
		 
		 /**
		 * Handles division dropdown click event
		 *
		 * @since    1.0.8
		*/		 
		 jQuery("#ic_division").on("change",function(){
		 			 						
			 	var ic_division_str=jQuery("#ic_division").val();	  				  			
	  			if(ic_division_meta_data[ic_division_str]!=undefined)
	  				{
			  			DisplayQuranicDivisionsDropDowns();
			  		}
				else
			  		{
			  			FetchDropdownData(); 				
			  		}				
	  			ShowHideFormDropdowns();
		 });
		 
		 /**
		 * Handles division number dropdown click event
		 *
		 * @since    1.0.8
		*/		 
		 jQuery("#ic_division_number").on("change",function(){
		 			 						
			 	DisplaySurahDropDown();
  				
				DisplayAyatDropDown();
		 });
		 
		 /**
		 * Handles sura dropdown click event
		 *
		 * @since    1.0.2
		*/	
		 jQuery("#ic_sura").on("change",function(){
		 	
		 		DisplayRukuDropDown();
		 					 	
	  			ShowHideFormDropdowns();
	  			
	  			DisplayAyatDropDown();
		 });
		 
		if(jQuery("#ic_form").html()!=undefined)FetchDropdownData();
	 });
})( jQuery );