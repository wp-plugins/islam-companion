<?php

class IslamCompanionHTTPAPI
	{
/****************************************************************************************************************/	
		private $option;
		private $language;
		private $link;		
/****************************************************************************************************************/		
		function __construct()
			{
				error_reporting(E_ALL);
				ini_set("display_errors",1);
				
				include_once("class-encryption.php");
		
				$this->link=mysqli_connect('localhost', 'nadir', 'kcbW5eFSCbPXbJGLHvUGG8T8','dev_pakjiddat_com');
				
				$this->option=base64_decode(urldecode($_GET['option']));
				
				if($this->option=="")die("Invalid api function call");
				
				if(isset($_GET['lang'])&&!defined("API_TEST"))
					{
						$this->language=base64_decode(urldecode($_GET['lang']));
						$insert_str="INSERT INTO ic_api_usage(ip_address,language,created_on) VALUES('".mysqli_real_escape_string($this->link,$_SERVER['REMOTE_ADDR'])."','".	mysqli_real_escape_string($this->link,$this->language)."','".mysqli_real_escape_string($this->link,time())."')";	
						mysqli_query($this->link,$insert_str);
					}
			}
/****************************************************************************************************************/
		function HandleAPICall()
			{
				try
					{
						$api_response=array();
						$encryption = new Encryption();
						
						if($this->option=="get_sura_names")$api_response=$this->GetSuraNames();
						else if($this->option=="get_distinct_languages")$api_response=$this->GetDistinctLanguage();
						else if($this->option=="get_distinct_languages_translators")$api_response=$this->GetDistinctLanguageTranslators();
						else if($this->option=="get_sura_verses")$api_response=$this->GetSuraVerses();
						else if($this->option=="get_random_verse")$api_response=$this->GetRandomVerse();
						else if($this->option=="get_language_information")$api_response=$this->GetLanguageInformation();
						else if($this->option=="get_division_meta")$api_response=$this->GetDivisionNumberDropdown();
						else if($this->option=="get_division_dropdowns")$api_response=$this->GetDivisionDropdowns();
						else if($this->option=="get_division_start_information")$api_response=$this->GetDivisionStartInformation();
												
						echo $encryption->EncryptText(json_encode($api_response));
						exit;
					}
				catch(exception $e)
					{
						echo $encryption->EncryptText(json_encode(array("result"=>'error',"text"=>"An error occurred in Islam Companion Plugin API. Details: ".$e->getMessage())));		
						exit;
					}	
			}
/****************************************************************************************************************/
		function GetDBLink()
			{
				return $this->link;
			}			
/****************************************************************************************************************/
		function GetSuraNames()
			{
				$sura_arr=array();
				$select_str = "SELECT tname,ayas,rukus FROM ic_quranic_suras_meta";
				$result=mysqli_query($this->link,$select_str);
				while($row=mysqli_fetch_assoc($result))
					{
						$sura_name=($row["tname"]);
						$ayas=($row["ayas"]);
						$rukus=($row["rukus"]);
						$sura_arr[]=array("sura"=>($sura_name),"ayas"=>($ayas),"rukus"=>($rukus));
					}								
			
				return array("result"=>'success',"text"=>$sura_arr);				
			}
/****************************************************************************************************************/			
		function GetDistinctLanguage()
			{
				$this->language_arr=array();
				$select_str = "SELECT DISTINCT language FROM ic_quranic_author_meta ORDER BY language";
				$result=mysqli_query($this->link,$select_str);
				while($row=mysqli_fetch_assoc($result))
					{
						$this->language=($row["language"]);
						$this->language_arr[]=array("language"=>($this->language));
					}
				
				return array("result"=>'success',"text"=>$this->language_arr);				
			}
/****************************************************************************************************************/
		function GetDistinctLanguageTranslators()
			{
				$this->language_translator_arr=array();
				$select_str = "SELECT language,translator FROM ic_quranic_author_meta";
				$result=mysqli_query($this->link,$select_str);
				while($row=mysqli_fetch_assoc($result))
					{
						$this->language=($row["language"]);
						$translator=($row["translator"]);
						$this->language_translator_arr[]=array("language"=>($this->language),"translator"=>utf8_encode($translator));
					}
				
				return array("result"=>'success',"text"=>$this->language_translator_arr);				
			}		
/****************************************************************************************************************/
		function GetDivisionNumber($table_name,$division_index_field,$sindex,$ayat)
			{				
				$division_number=0;				
				$division_number_arr=array();
				$result=mysqli_query($this->link,"SELECT * FROM ".$table_name." WHERE sura=".mysqli_real_escape_string($this->link,$sindex)." AND aya<=".mysqli_real_escape_string($this->link,$ayat)." ORDER BY aya DESC");
				$row=mysqli_fetch_assoc($result);
				if(isset($row['sura']))$division_number=$row[$division_index_field];
				else 
					{
						$result=mysqli_query($this->link,"SELECT * FROM ".$table_name." WHERE sura<".mysqli_real_escape_string($this->link,($sindex))." ORDER BY sura DESC");
						$row=mysqli_fetch_assoc($result);
						$division_number=$row[$division_index_field];
					}
					
				return $division_number;					
			}					
/****************************************************************************************************************/
		function GetSuraVerses()
			{
				$this->language=base64_decode(urldecode($_GET['lang']));
				$translator=base64_decode(urldecode($_GET['narrator']));
				$sura=base64_decode(urldecode($_GET['sura']));				
				$division=base64_decode(urldecode($_GET['division']));				
				$ayat=base64_decode(urldecode($_GET['ayat']));
				
				if($this->language==""||$translator=="")die("Invalid api function call");
				
				$ruku=$division_number="";
				$select_str="SELECT sindex,audiofile FROM ic_quranic_suras_meta WHERE tname='".mysqli_real_escape_string($this->link,$sura)."'";						
				$result=mysqli_query($this->link,$select_str);				
				$row=mysqli_fetch_assoc($result);
				$sindex=$row['sindex'];
				$audiofile=$row['audiofile'];
			
				$ruku=0;
				$select_str="SELECT rindex,aya FROM ic_quranic_rukus_meta WHERE sura='".mysqli_real_escape_string($this->link,$sindex)."' ORDER BY aya ASC";						
				$result=mysqli_query($this->link,$select_str);				
				while($row=mysqli_fetch_assoc($result))				
					{														
						$aya=$row['aya'];
						if($aya>$ayat)break;
						else $ruku++;
					}
				
				$ayat_count=$start_ayat=$count=0;
				$select_str="SELECT aya FROM ic_quranic_rukus_meta WHERE sura='".mysqli_real_escape_string($this->link,$sindex)."' ORDER BY rindex ASC";		
				$result=mysqli_query($this->link,$select_str);				
				while($row=mysqli_fetch_assoc($result))
					{
						$count++;
						if($count==$ruku)$start_ayat=$row['aya'];
						else if($count==($ruku+1))$ayat_count=(isset($row['aya']))?($row['aya']-$start_ayat):'6';
					}
						
				$select_str="SELECT * FROM ic_quranic_author_meta WHERE language='".mysqli_real_escape_string($this->link,$this->language)."' AND translator='".mysqli_real_escape_string($this->link,$translator)."'";
				$result=mysqli_query($this->link,$select_str);
				$row=mysqli_fetch_assoc($result);
		
				$ayat_text_arr="";
				$select_str="SELECT * FROM ic_quranic_text WHERE file_name='".mysqli_real_escape_string($this->link,$row['file_name'])."' AND surah='".mysqli_real_escape_string($this->link,$sindex)."'";
				$select_str.=" LIMIT ".($start_ayat-1);
				if($ayat_count!='0')$select_str.=",".$ayat_count;
				else $select_str.=",1000";
				
				$end_ayat="-1";
				$result=mysqli_query($this->link,$select_str);		
				while($row=mysqli_fetch_assoc($result))
					{
						$end_ayat=$row['ayat'];						
						$ayat_text_arr[]=$row['text'];
					}
					
				$ayat_text=implode("~",$ayat_text_arr);
						
				$result=mysqli_query($this->link,"SELECT rindex FROM ic_quranic_rukus_meta WHERE sura='".mysqli_real_escape_string($this->link,$sindex)."' AND aya='".mysqli_real_escape_string($this->link,($start_ayat))."'");
				$row=mysqli_fetch_assoc($result);
				$current_ruku_index=$row['rindex'];
				
				$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_rukus_meta WHERE rindex='".mysqli_real_escape_string($this->link,($current_ruku_index+1))."'");
				$row1=mysqli_fetch_assoc($result);
				$next_ruku_index=(isset($row1['rindex']))?$row1['rindex']:"1";
				
				if($next_ruku_index=="1")
					{
						$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_rukus_meta WHERE rindex='1'");
						$row1=mysqli_fetch_assoc($result);
					}
				
				$next_sindex=$row1['sura'];
				$next_ayat=$row1['aya'];
				
				$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_rukus_meta WHERE rindex='".mysqli_real_escape_string($this->link,($current_ruku_index-1))."'");
				$row2=mysqli_fetch_assoc($result);
				$prev_ruku_index=(isset($row2['rindex']))?$row2['rindex']:"556";
				
				if($prev_ruku_index=="556")
					{
						$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_rukus_meta WHERE rindex='556'");
						$row2=mysqli_fetch_assoc($result);
					}
				
				$prev_sindex=$row2['sura'];
				$prev_ayat=$row2['aya'];
				
				if($division=="sura")$division="ruku";
				$table_name="ic_quranic_".$division."s_meta";				
				$division_index_field=substr($division,0,1)."index";

				$division_number=$this->GetDivisionNumber($table_name,$division_index_field,$sindex,$ayat);
				$next_division=$this->GetDivisionNumber($table_name,$division_index_field,$next_sindex,$next_ayat);
				$prev_division=$this->GetDivisionNumber($table_name,$division_index_field,$prev_sindex,$prev_ayat);
						
				$select_str="SELECT * FROM ic_quranic_suras_meta WHERE sindex='".mysqli_real_escape_string($this->link,$next_sindex)."'";		
				$result=mysqli_query($this->link,$select_str);				
				$row=mysqli_fetch_assoc($result);
				$next_sura=$row['tname']."~".$row['ayas']."~".$row['rukus'];		
				$next_sura_ruku_count=$row['rukus'];
				
				$select_str="SELECT * FROM ic_quranic_suras_meta WHERE sindex='".mysqli_real_escape_string($this->link,$prev_sindex)."'";		
				$result=mysqli_query($this->link,$select_str);				
				$row=mysqli_fetch_assoc($result);
				$prev_sura=$row['tname']."~".$row['ayas']."~".$row['rukus'];		
				$prev_sura_ruku_count=$row['rukus'];
				
				$select_str="SELECT * FROM ic_quranic_suras_meta WHERE sindex='".mysqli_real_escape_string($this->link,$sindex)."'";		
				$result=mysqli_query($this->link,$select_str);				
				$row=mysqli_fetch_assoc($result);
				$sura_ename=$row['ename'];
				$sura=$row['tname'];	
				$sura_ruku_count=$row['rukus'];
				
				if($ruku<$sura_ruku_count)$next_sura_ruku=($ruku+1);
				else $next_sura_ruku="1";
				
				if($ruku=="1")$prev_sura_ruku=($prev_sura_ruku_count);
				else $prev_sura_ruku=($ruku-1);
				
				$navigation_information=array(
							"next_navigation"=>array("sura"=>$next_sura,"ayat"=>$next_ayat,"sindex"=>$next_sindex,"division_number"=>$next_division,"ruku_number"=>$next_ruku_index,"sura_ruku"=>$next_sura_ruku),
							"prev_navigation"=>array("sura"=>$prev_sura,"ayat"=>$prev_ayat,"sindex"=>$prev_sindex,"division_number"=>$prev_division,"ruku_number"=>$prev_ruku_index,"sura_ruku"=>$prev_sura_ruku)
				);
									
				return array("result"=>'success',"audiofile_name"=>$audiofile,"text"=>$ayat_text,"start_ayat"=>$start_ayat,"end_ayat"=>$end_ayat,"sura_ename"=>$sura_ename,"division_information"=>$navigation_information);				
			}
/****************************************************************************************************************/
		function GetRandomVerse()
			{
				$this->language=base64_decode(urldecode($_GET['lang']));
				$translator=base64_decode(urldecode($_GET['narrator']));
				
				if($this->language==""||$translator=="")die("Invalid api function call");
				$select_str="SELECT * FROM ic_quranic_author_meta WHERE language='".mysqli_real_escape_string($this->link,$this->language)."' AND translator='".mysqli_real_escape_string($this->link,$translator)."'";
				$result=mysqli_query($this->link,$select_str);
				$row=mysqli_fetch_assoc($result);
				
				$current_day_timestamp=strtotime(date("d")."-".date("m")."-".date("y"));
				
				$select_str="SELECT * FROM ic_quranic_text WHERE file_name='".mysqli_real_escape_string($this->link,$row['file_name'])."' ORDER BY RAND(".$current_day_timestamp.") LIMIT 0,1";
				$result=mysqli_query($this->link,$select_str);
				$row=mysqli_fetch_assoc($result);
		
				return array("result"=>'success',"text"=>$row['text']);				
			}
/****************************************************************************************************************/
		function GetLanguageInformation()
			{
				$this->language=base64_decode(urldecode($_GET['lang']));
				
				if($this->language=="")die("Invalid api function call");
				
			    $result=mysqli_query($this->link,"SELECT rtl,css_attributes,dictionary_url FROM ic_quranic_author_meta WHERE language='".mysqli_real_escape_string($this->link,$this->language)."'");
				$row=mysqli_fetch_assoc($result);
				
				$dictionary_url=$row['dictionary_url'];		
				$is_rtl=$row['rtl'] ? 'true' : 'false';
				$css_class=$row['css_attributes'];
				$text=array("rtl"=>$is_rtl,"css_class"=>$css_class,"dictionary_url"=>$dictionary_url);
				
				return array("result"=>'success',"text"=>$text);		
			}
/****************************************************************************************************************/
		function GetDivisionStartInformation()
			{
				$division=base64_decode(urldecode($_GET['division']));
				$division_number=base64_decode(urldecode($_GET['division_number']));
				$sura=base64_decode(urldecode($_GET['sura']));
				$ruku=base64_decode(urldecode($_GET['ruku']));
			
			    $table_name="ic_quranic_".$division."s_meta";
			    $division_index_field=substr($division,0,1)."index";
				if($division!="sura")
					{
						if($sura=="N.A")
							{
								$result=mysqli_query($this->link,"SELECT * FROM ".$table_name." WHERE id='".mysqli_real_escape_string($this->link,$division_number)."' ORDER BY aya ASC");
								$row=mysqli_fetch_assoc($result);
								$start_sura=$row['sura'];
								$start_ayat=$row['aya'];
							}
						else 
							{
								$result=mysqli_query($this->link,"SELECT * FROM ".$table_name." WHERE id='".mysqli_real_escape_string($this->link,$division_number)."' AND sura='".mysqli_real_escape_string($this->link,$sura)."' ORDER BY aya ASC");
								$row=mysqli_fetch_assoc($result);								
								$start_ayat=(isset($row['aya']))?$row['aya']:1;
								$start_sura=$sura;
								
								if($ruku!="N.A")
									{
										$start_ruku=$ruku;
										$counter=0;
										$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_rukus_meta WHERE sura='".mysqli_real_escape_string($this->link,$sura)."' ORDER BY rindex ASC");
										while($row=mysqli_fetch_assoc($result))
											{
												$counter++;
												if($counter==$ruku)$start_ayat=$row['aya'];
											}
									}
							}
						
						if($ruku=="N.A")
							{
								$start_ruku=1;
								$counter=0;
								$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_rukus_meta WHERE sura='".mysqli_real_escape_string($this->link,$start_sura)."' ORDER BY aya ASC");
								while($row=mysqli_fetch_assoc($result))
									{								
										$counter++;
										if($start_ayat==$row['aya'])
											{
												$start_ruku=$counter;
												break;
											}
										else if($start_ayat<$row['aya'])
											{
												$start_ruku=($counter-1);
												break;
											}
									}
								
								if($start_ayat>$row['aya'])$start_ruku=$counter;
							}
							
						if($ruku!="N.A")$division_number=$this->GetDivisionNumber($table_name,$division_index_field,$sura,$start_ayat);															
					}
				else 
					{
						$start_ruku=$ruku;
						$start_ayat=1;
						$start_sura=$sura;
						$counter=0;
						$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_rukus_meta WHERE sura='".mysqli_real_escape_string($this->link,$sura)."' ORDER BY rindex ASC");
						while($row=mysqli_fetch_assoc($result))
							{
								$counter++;
								if($counter==$ruku)$start_ayat=$row['aya'];
							}		

						$division_number=$start_sura;
				}

				$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_suras_meta WHERE sindex='".mysqli_real_escape_string($this->link,$start_sura)."'");
				$row=mysqli_fetch_assoc($result);
				$sura_name=$row['tname'];
				$total_ayat=$row['ayas'];
				$total_rakaat=$row['rukus'];
				$sura=$sura_name."~".$total_ayat."~".$total_rakaat;
				$sindex=$start_sura;
				$ayat=$start_ayat;
				$sura_ruku=$start_ruku;
				$data=array("sura"=>$sura,
									"total_ayat"=>$total_ayat,
									"total_rakaat"=>$total_rakaat,
									"sindex"=>$sindex,
									"ayat"=>$ayat,
									"division_number"=>$division_number,
									"sura_ruku"=>$sura_ruku);
						
				return array("result"=>'success',"data"=>$data);		
			}			
/****************************************************************************************************************/
		function GetDivisionDropdowns()
			{
				$division=base64_decode(urldecode($_GET['division']));
				$division_number=base64_decode(urldecode($_GET['division_number']));
				$sura=base64_decode(urldecode($_GET['sura']));
				$ruku_number=base64_decode(urldecode($_GET['ruku_number']));
				$dropdown_type=base64_decode(urldecode($_GET['dropdown_type']));
				
				$max_division_number=-1;
				$start_sura="1";
				$end_sura="114";
				$start_ruku=1;
				if($division=="hizb")$max_division_number=240;
				else if($division=="juz")$max_division_number=30;
				else if($division=="manzil")$max_division_number=7;
				else if($division=="page")$max_division_number=604;

				$result=mysqli_query($this->link,"SELECT sindex FROM ic_quranic_suras_meta WHERE tname='".mysqli_real_escape_string($this->link,$sura)."'");
				$row=mysqli_fetch_assoc($result);
				$sura_number=$row['sindex'];
					
				if($division!="sura")
					{
						$table_name="ic_quranic_".$division."s_meta";
						$result=mysqli_query($this->link,"SELECT sura,aya FROM ".$table_name." WHERE id='".mysqli_real_escape_string($this->link,$division_number)."'");
						$row=mysqli_fetch_assoc($result);
						$start_sura=$row['sura'];
						
						$result=mysqli_query($this->link,"SELECT aya FROM ".$table_name." WHERE id='".mysqli_real_escape_string($this->link,$division_number)."' AND sura='".mysqli_real_escape_string($this->link,$sura_number)."' ORDER BY aya ASC");
						$row=mysqli_fetch_assoc($result);
						$start_ayat=(isset($row['aya']))?$row['aya']:1;						
					
						$result=mysqli_query($this->link,"SELECT sura,aya FROM ".$table_name." WHERE id='".mysqli_real_escape_string($this->link,$division_number+1)."'");
						$row=mysqli_fetch_assoc($result);
						if(isset($row))
							{
								$end_sura=$row['sura'];
								$end_ayat=$row['aya'];
								if($end_ayat=="1")$end_sura=($end_sura-1);
							}
						else 
							{
								$end_sura="114";
								$end_ayat="1";
							}
							
						$counter=0;
						$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_rukus_meta WHERE sura='".mysqli_real_escape_string($this->link,$sura_number)."' ORDER BY aya ASC");
						while($row=mysqli_fetch_assoc($result))
							{								
								$counter++;
								if($start_ayat>=$row['aya'])
									{
										$start_ruku=$counter;
										break;
									}
							}											
					}

				$counter=0;
				$max_ruku=0;
				$sura_box="<select name='ic_sura_box' id='ic_sura_box' onchange='DropdownUpdate(\"ic_sura_box\");'>\n";
				$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_suras_meta WHERE id>='".mysqli_real_escape_string($this->link,$start_sura)."' AND id<='".mysqli_real_escape_string($this->link,$end_sura)."'");
				while($row=mysqli_fetch_assoc($result))
					{
						if($row['sindex']==$sura_number)
							{
								$max_ruku=$row['rukus'];
								$sura_box.="<option value='".$row['sindex']."' SELECTED>".$row['tname']."</option>\n";
							}
						else $sura_box.="<option value='".$row['sindex']."'>".$row['tname']."</option>\n";
						$counter++;
					}
				$sura_box.="</select>";
				
				$division_number_box="<select name='ic_division_number_box' id='ic_division_number_box' onchange='DropdownUpdate(\"ic_division_number_box\");'>\n";
				for($count=1;$count<=$max_division_number;$count++)
					{
						if($count==$division_number)$division_number_box.="<option value='".$count."' SELECTED>".$count."</option>\n";
						else $division_number_box.="<option value='".$count."'>".$count."</option>\n";						
					}
				$division_number_box.="</select>";

				$ruku_number_box="<select name='ic_ruku_number_box' id='ic_ruku_number_box' onchange='DropdownUpdate(\"ic_ruku_number_box\");'>\n";
				for($count=$start_ruku;$count<=$max_ruku;$count++)
					{
						if($count==$ruku_number)$ruku_number_box.="<option value='".$count."' SELECTED>".$count."</option>\n";
						else $ruku_number_box.="<option value='".$count."'>".$count."</option>\n";						
					}
				$ruku_number_box.="</select>";
				
				$dropdown_boxes=array();
				if($dropdown_type=="all"||$dropdown_type=="division_number_box")$dropdown_boxes["division_number_box"]=$division_number_box;
				if($dropdown_type=="all"||$dropdown_type=="sura_box")$dropdown_boxes["sura_box"]=$sura_box;
				if($dropdown_type=="all"||$dropdown_type=="ruku_box")$dropdown_boxes["ruku_box"]=$ruku_number_box;
				
				return array("result"=>'success', "dropdown_boxes"=>$dropdown_boxes);				
			}						
/****************************************************************************************************************/			
		function GetDivisionNumberDropdown()
			{
				$division=base64_decode(urldecode($_GET['division']));
				$division_number=base64_decode(($_GET['division_number']));
				if(strpos($division_number,"~")===false)$division_number="1";
				else list($division_number,$surah_number,$ayat_number)=explode("~",$division_number);				
				
				if($division=="")die("Invalid api function call");
				
			    if($division=="hizb")$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_hizbs_meta");
				else if($division=="juz")$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_juzs_meta");
				else if($division=="manzil")$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_manzils_meta");
				else if($division=="page")$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_pages_meta");
							
				$division_arr=array();		
				while($row=mysqli_fetch_assoc($result))$division_arr[]=array("surah"=>$row['sura'],"aya"=>$row['aya']);
				
				$result=mysqli_query($this->link,"SELECT * FROM ic_quranic_suras_meta");
							
				$surah_arr=array();	 	
				while($row=mysqli_fetch_assoc($result))$surah_arr[]=array("ayas"=>$row['ayas']);
				
				$options_arr=array();				
				for($count=0;$count<count($division_arr);$count++)
					{
						$division_item=$division_arr[$count];
						$temp_division_number=($count+1)."~".$division_item['surah']."~".$division_item['aya'];
						
						if(($count+1)==$division_number)$options_arr[]=$temp_division_number."!".ucfirst($division)." ".($count+1)."!SELECTED@";
						else $options_arr[]=$temp_division_number."!".ucfirst($division)." ".($count+1)."!@";						
					}
				
				return array("result"=>'success',"text"=>trim(implode("",$options_arr)));				
			}
/****************************************************************************************************************/						
	}
?>