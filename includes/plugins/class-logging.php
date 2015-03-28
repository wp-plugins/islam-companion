<?php
/**
 * The file that allowing logging/error handling
 *
 * A class definition that includes attributes and functions used for logging/error handling.
 *
 * @link:      http://nadirlatif.me/islam-companion
 * @since      1.0.5
 *
 * @package    Islam_Companion
 * @subpackage Islam_Companion/includes
 */

/**
 * The class for Logging/Error Handling.
 *
 * This is used to define functions for logging and error handling.
 * It handles uncaught exceptions and errors.
 * It can send an email to the plugin author in case of errors
 *
 * It registers functions that are automatically called when an error or exception occurs
 * 
 *
 * @since      1.0.5
 * @package    Islam_Companion
 * @subpackage Islam_Companion/includes
 * @author:    Nadir Latif <nadir@nadirlatif.me>
 */
class Logger
	{
/*****************************************************************************************************************/
		private $error_level;
		private $error_message,$error_file,$error_line,$error_context;
		private $user_email;
		private $log_file_name;
		private $type;
		private $log_error_header;
		private $exception_obj;
/*****************************************************************************************************************/
		public function __construct()
			{
				$this->user_email=LOG_ERROR_EMAIL;
				$this->log_file_name=LOG_FILE_NAME;
				$this->log_error_header=LOG_ERROR_HEADER;
				$this->type="";
			}
/*****************************************************************************************************************/
		/**
		 * Function used to handle errors. It should be registered as error handling function
		 *
		 * @since    1.0.5
		 */			
		public function ErrorHandler($error_level,$error_message,$error_file,$error_line,$error_context)
			{
				try
					{
						$this->error_level=$error_level;
						$this->error_message=$error_message;
						$this->error_file=$error_file;
						$this->error_line=$error_line;
						$this->error_context=$error_context;
						$this->type="Error";
						$this->LogError();
					}
				catch(exception $e)
					{
						throw new Exception($e->getMessage());
					}
			}
/*****************************************************************************************************************/	
		/**
		 * Function used to handle exceptions. It should be registered as exception handling function
		 *
		 * @since    1.0.5
		 */
		public function ExceptionHandler($exception_obj)
			{
				try
					{
						$log_message="";
						$response=array("result"=>"error","data"=>$this->error_message);														
						$this->$exception_obj=$exception_obj;
						$this->error_level=$exception_obj->getCode();
						$this->error_message=$exception_obj->getMessage();
						$this->error_file=$exception_obj->getFile();
						$this->error_line=$exception_obj->getLine();
						$this->error_context=$exception_obj->getTrace();
						$this->type="Exception";
						$this->LogError();
					}
				catch(exception $e)
					{
						$response['data']=nl2br($response['data']);
						if(DEBUG)echo json_encode(array("result"=>'error',"text"=>$response));
						else _e("An error occurred in the Islam Companion Plugin. Please try again.","islam-companion");													
						exit;
					}
			}
/*****************************************************************************************************************/	
		/**
		 * Function that is called when execution of the current page ends. It should be registered as shutdown function
		 *
		 * @since    1.0.5
		 */
		public function ShutdownFunction()
			{
				$error = error_get_last();
    			if(isset($error["type"]))$this->ErrorHandler($error["type"],$error["message"],$error["file"],$error["line"],"Fatal error in script");        	
			}			
/*****************************************************************************************************************/	
		/**
		 * Function that is used to log errors. It is called by error handling exception handling functions.
		 * It can save error message to log file to send error by email
		 * 
		 * @since    1.0.5
		 */
		private function LogError()
			{
				try
					{
						$log_message="";
						if(strpos($this->error_file,"islam-companion")===false)return;
						$response=array("result"=>"error","data"=>$this->error_message);						
						$log_message="Exception on: ".date("d-m-Y H:i:s");
						$log_message.="\nError Level: ".$this->error_level;
						$log_message.="\nError Message: ".$this->error_message;
						$log_message.="\nError File: ".$this->error_file;
						$log_message.="\nError Line: ".$this->error_line."\n\n";											
						if(is_array($this->error_context))$log_message.="\nError Back Trace: ".json_encode($this->error_context)."\n\n";
						else $log_message.="\nError Back Trace: ".($this->error_context)."\n\n";
						$response['data']=nl2br($log_message);					
						if($this->log_file_name)error_log($log_message,3,$this->log_file_name);
						if($this->user_email)error_log($log_message,1,$this->user_email,$this->log_error_header);

						echo json_encode($response);
						exit;						
						//if($this->type=="Exception"||($this->type=="Error"&&$this->error_level==E_ERROR))exit;						
					}
				catch(exception $e)
					{
						$response['data']=nl2br($response['data']);
						if(DEBUG)echo json_encode($response);
						else _e("An error occurred in the Islam Companion Plugin. Please try again.","islam-companion");				
						exit;
					}				
			}				
/*****************************************************************************************************************/	
	}

?>