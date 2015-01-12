var current_sura;
var current_aya;

function ValidateICSettingsForm()
	{
		var ic_language_str=document.getElementById('ic_language').value;
		var ic_narrator_str=document.getElementById('ic_narrator').value;
		var ic_sura_str=document.getElementById('ic_sura').value;
		var ic_aya_str=document.getElementById('ic_aya').value;
		var ic_ayat_count_str=document.getElementById('ic_ayat_count').value;
		
		if(ic_language_str==""){alert("Please select a language");return false;}
		if(ic_narrator_str==""){alert("Please select a narrator");return false;}
		if(ic_sura_str==""){alert("Please select a sura");return false;}
		if(ic_aya_str==""){alert("Please select an ayat");return false;}
		if(ic_ayat_count_str==""){alert("Please the ayat count");return false;}
		return true;
	}

function FetchVerseData()
	{
		$.ajax({
				  url: "test.html",
				  context: document.body
				}).done(function() {
				  $( this ).addClass( "done" );
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
		 
		 $("#ic_language").on("change",function(){
			 	$('#ic_narrator').empty();
			 	var ic_language_str=$("#ic_language").val();	  			
	  			var ic_narrator_hidden_str=$("#ic_narrator_hidden").val();	  				  			
	  			var temp_arr=ic_narrator_hidden_str.split("@");
	  			
	  			if(ic_language_str!="")$('#ic_narrator').append($('<option></option>').val("").html("--Please Select--"));
	  			else $('#ic_narrator').append($('<option></option>').val("").html("--Please select a language first--"));
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
		 
		 $("#ic_sura").on("change",function(){
			 	$('#ic_aya').empty();
			 	var ic_sura_str=$("#ic_sura").val();	  			
			 	ic_sura_str=ic_sura_str.replace('\\','');	  			
	  			var temp_arr=ic_sura_str.split("~");
	  			var ayas=parseInt(temp_arr[1]);
	  			
	  			if(ic_sura_str!="")$('#ic_aya').append($('<option></option>').val("").html("--Please Select--"));
	  			else $('#ic_sura').append($('<option></option>').val("").html("--Please select a sura first--"));
	  			for(var count=1;count<=ayas;count++)
	  				{
		  				$('#ic_aya').append($('<option></option>').val(count).html(count));		  				
	  				}
		 });
	 });
})( jQuery );