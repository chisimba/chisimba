<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
 * This class contains the mailer information 
 *
 * @category  Chisimba
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @package utilities
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

require("phpmailer_class_inc.php");

class mailer extends phpmailer 
{
    // Set default variables for all new objects
    /**
    * @var $From
    * Property to hold the email address of the sender
    */
    var $From; //    = "paul@example.com";
    /**
    * @var $FromName
    * Property to hold the name of the sender
    */
    var $FromName;// = "PHP Mass mailer";
    /**
    * @var $Host
    * Property to hold the host
    */
    var $Host; //     = "mail.server.co.za";
    /**
    * @var $Mailer
    * Property to hold the type of mail
    */
    var $Mailer; //   = "sendmail";                         // Alternative to IsSMTP()
    /**
    * @var $WordWrap
    * Property to hold the amount of word wrapping
    */
    var $WordWrap; // = 75;
    
    /**
    * Set the mailer object
    *
    * @param string $From
    * @param string $FromName
    * @param string $Host
    * @param string $Mailer
    * @param string $WordWrap
    */
    function mailer($From, $FromName, $Host, $Mailer, $WordWrap)
    {
    	$this->From = $From;
		$this->FromName = $FromName;
		$this->Host = $Host;
		$this->Mailer = $Mailer;
		$this->WordWrap = $WordWrap;
    }

    
    /**
    * Replace the default error_handler
    *
    * @param string $msg
    */
    function error_handler($msg) {
        //print("Mail Error");
        //print("Description:");
        printf("%s", $msg);
        exit;
        
        echo $From,$FromName,$Host,$Mailer,$WordWrap;
    }

}
?>