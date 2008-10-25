<?php
/**
 * TimeoutMessage class
 * 
 * Used to create messages with a timer
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
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Jonathan Abrahams
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
*
* Used to create messages with a timer.
*
* @category  HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   GNU GPL
* @author    Jonathan Abrahams
*            
*/
class timeoutMessage extends object {
    /**
    * @var string : The confirmation message.
    */
    public $message;
    /**
    * @var integer: The timer (in miliseconds) used timeout the message.
    */
    public $timeout;
    /**
    * @var string: The css id
    */
    public $cssId;
    /**
    * @var string: The css class
    */
    public $cssClass;
    /**
    * @var string: The html tag
    */
    public $htmlTag;
    /**
    * @var string: the CSS Method of hiding - either NONE OR HIDDEN
    */
    public $typeHide;

    /**
    * Initialization method to set default values
    */
    public function init( ) {
        //The default message is empty.
        $this->message = '';
        //The default timeout is 5seconds.
        $this->timeout = 5000;
        //The default cssId is #confirm
        $this->cssId = 'confirm';
        //The default htmlTag is SPAN
        $this->htmlTag = 'span';
        // The default type of hide
        $this->typeHide = 'none';
        //hidden
	}

    /*
	* Method to set the message
	* @param string
	*/
    public function setMessage( $message ) {
        $this->message = $message;
    }

    /*
	 * Method to set the timeout.
	 * @param integer unit in miliseconds.
     * @param  unknown $miliSec Parameter description (if any) ...
     * @return void   
     * @access public 
     */
    public function setTimeout( $miliSec ) {
        $this->timeout = $miliSec;
    }

    /*
	 * Method to set the hide type to Hidden.
     * @return void  
     * @access public
     */
    public function setHideTypeToHidden( ) {
        $this->typeHide = 'hidden';
    }

    /*
	 * Method to set the hide type to Hidden.
     * @return void  
     * @access public
    */
    public function setHideTypeToNone( ) {
        $this->typeHide = 'none';
    }

    /*
	 * Private method to insert the java script function.
     * @return string Return description (if any) ...
     * @access public
    */
    public function _jscript() {
        static $count = 0;
        $jscript = '<script type="text/javascript">';

        if ($this->typeHide == 'hidden') {
            $code = '.visibility = \'hidden\'';
        } else {
            $code = '.display = \'none\'';
        }

        $jscript.= "function hidemydiv$count() {
            var el = document.getElementById('$this->cssId');
            if(el){
                el.style$code;
            }
        }";
        $jscript.= " setTimeout('hidemydiv$count()', $this->timeout );";
        $jscript.="</script>";
        $count++;
        return $jscript;
    }

    /*
	 * Method to show jscript in header.
     * @return void  
     * @access public
     */
	public function showJScript()
	{
        $this->appendArrayVar('headerParams', $this->_jscript() );
    }

    /*
	 * Method to show the message.
     * @return string Return description (if any) ...
     * @access public
     */
	public function show()
	{
        // Timeout script is Disable for invalid values
        if( $this->timeout > 0 ) {
            $this->showJScript();
        }
        $str = sprintf( "<%s id=\"%s\">", $this->htmlTag, $this->cssId );
        $str.= $this->message;
        $str.= "</$this->htmlTag>";
		return $str;
	}
}
?>
