<?php
/* --------------------------- engine class ------------------------*/

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
 * This class is used to show a confirmation box, it uses the javascript confirm 
 *
 * @category  Chisimba
 * @package   utilities
 * @author Wesley Nitsckie
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class confirm extends object{

    /**
    *@var $message
    *The message that will be desplayed in the confirm box
    */
    var $message;

    /**
    *@var $url
    *The url
    */
    var $url;

    /**
    *@var $link
    *The link .. can be text or image
    */
    var $link;

    /**
    *@var $extra
    *Any extra subelements go here
    */
    var $extra;

    /**
    *Constructor
    */
    function confirm(){
        $this->init();
    }

    /**
    *Initialize
    */
    function init(){

    }

    /**
    *Method to setup the Confirmation Box
    *@param string $link: The Link
    *@param string $url :the url
    *@param $string $message: The message that will be displayed in the box
        * @param string $extra - any extra stuff goes here
    */
    function setConfirm($link=NULL,$url=NULL,$message=NULL,$extra=NULL){
        $this->link=$link;
        $this->url=$url;
        $this->message=$message;
                $this->extra=$extra;
    }


    /**
    *Method to show the box
    */
    function show(){
        $str='<a href="javascript: if(confirm(\''.$this->message.'\')) {document.location=\''.$this->url.'\'}" '.$this->extra.'>'.$this->link.'</a>';
        return $str;
    }
}
?>