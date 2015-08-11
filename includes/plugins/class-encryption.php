<?php

/**
 * The file that allowing encryption/decryption of text
 *
 * A class definition that includes attributes and functions used for encrypting/decrypting text.
 *
 * @link:       http://pakjiddat.com
 * @since      1.0.0
 *
 * @package    Islam_Companion
 * @subpackage Islam_Companion/includes
 */

/**
 * The class for Encryption/Decryption feature.
 *
 * This is used to define functions for encrypting and decrypting text.
 * It is used by different features such as Holy Quran Dashboard Widget.
 *
 * It uses built in php functions mcrypt_encrypt and mcrypt_decrypt 
 * 
 *
 * @since      1.0.0
 * @package    Islam_Companion
 * @subpackage Islam_Companion/includes
 * @author:       Nadir Latif <nadir@pakjiddat.com>
 */
class Encryption {
	/**
     * Holds the key used for encrypting and decrypting text
     */
    private $key;
    
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
	    # the key should be random binary, use scrypt, bcrypt or PBKDF2 to
	    # convert a string into a key
	    # key is specified using hexadecimal
	    try
	    	{
	    		$this->key = pack('H*', "c7ea6ad07a6bb93686bbfb64a592c1c23c6b6e35c17a9ab73ee6b3bc25f4cf08");
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
	
	/**
	 * Function used to encrypt given text
	 *
	 * @since    1.0.0
	 */
	public function EncryptText($text) {		
		try
			{
				# create a random IV to use with CBC encoding
			    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
			    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		
				# creates a cipher text compatible with AES (Rijndael block size = 128)
			    # to keep the text confidential 
			    # only suitable for encoded input that never ends with value 00h
			    # (because of default zero padding)
			    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $text, MCRYPT_MODE_CBC, $iv);
			
			    # prepend the IV for it to be available for decryption
			    $ciphertext = $iv_size.$iv.$ciphertext;
			    
			    # base64 encode the cipher text
			    $ciphertext = base64_encode($ciphertext);
			    
		    	return $ciphertext;
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}                 
	}
	
	/**
	 * Function used to decrypt given text
	 *
	 * @since    1.0.0
	 */
	public function DecryptText($ciphertext_base64) {		
		try
			{
				$ciphertext_dec = base64_decode($ciphertext_base64);
		    
				# retrieves the IV size
		    	$iv_size = substr($ciphertext_dec, 0, 2);
		    	
		    	# retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
		    	$iv_dec = substr($ciphertext_dec, 2, $iv_size);
		    
		    	# retrieves the cipher text (everything except the $iv_size in the front)
		    	$ciphertext_dec = substr($ciphertext_dec, $iv_size+2);
		
		    	# may remove 00h valued characters from end of plain text
		    	$plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
		
				return $plaintext_dec;
			}
		catch(Exception $e)
			{
				throw new Exception("Error in Islam Companion Plugin. Details: ".$e->getMessage());
			}
	}
}
?>