<?php
/**
 *
 * An interface to the re-captcha API for generating and using captchas
 *
 * An interface to the re-captcha API for generating and using captchas
 * by wrapping the reCAPTCHA PHP Lirary which helps you use the reCAPTCHA 
 * API. Documentation for this library can be found at
 *   http://recaptcha.net/plugins/php
 * The API key can be obtained at 
 *   http://recaptcha.net/api/getkey?app=php
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   twitter
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: twitterremote_class_inc.php 14230 2009-08-07 12:00:08Z paulscott $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 * An interface to the re-captcha API for generating and using captchas
 *
 * An interface to the re-captcha API for generating and using captchas
 * by wrapping the reCAPTCHA PHP Lirary which helps you use the reCAPTCHA 
 * API. Documentation for this library can be found at
 *   http://recaptcha.net/plugins/php
 * The API key can be obtained at 
 *   http://recaptcha.net/api/getkey?app=php
*
* @author Derek Keats <derek@dkeats.com>
* @package utilities
*
*/
class recaptcha extends object
{
	
    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;

    /**
    *
    * Constructor for the recaptcha  class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Load the recaptcha library.
        require_once($this->getResourceUri('recaptcha/recaptchalib.php', 'utilities'));
        
        // Load the language object.
        $this->objLanguage = $this->getObject('language', 'language');
        
        // Load the configuration object
        $objConfig = $this->getObject('altconfig', 'config');
        
        
        // Obtain the API key from testing
        #TODO
        // Determine if there is an API key
        #temporary
        $publicKey="NULL";
        #TODO
        if ($publicKey=="NULL") {
        	$publicKey=FALSE;
        }
        if ($privateKey=="NULL") {
            $privateKey=FALSE;
        }
        
        $this->publicKey=$publicKey;
        $this->privateKey=$privateKey;
    }

	/**
	* 
	* Method to wrap the recaptcha_get_html function in the 
	* recaptcha library
	* 
	* @access public
	* @return string Recaptcha image
	* 
    */
    public function getCaptcha() 
    {
    	$err = $this->objLanguage->languageText('mod_utilities_recaptchaerr', 'utilities');
    	return recaptcha_get_html($this->publicKey, $err);
    }

    /**
    * 
    * Method to wrap the recaptcha_check_answer function in the 
    * recaptcha library
    * 
    * @access public
    * @return boolean TRUE|FALSE true if the captcha was entered successfully, else false
    * 
    */
    public function checkAnswer() 
    {
        return recaptcha_check_answer ($this->publicKey,
          $_SERVER["REMOTE_ADDR"],
          $_POST["recaptcha_challenge_field"],
          $_POST["recaptcha_response_field"]);
    }
    
    publicFunction hideEmail()
    {
    	
    }

}
?>
