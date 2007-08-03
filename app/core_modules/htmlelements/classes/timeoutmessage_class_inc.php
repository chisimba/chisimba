<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
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

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $message Parameter description (if any) ...
     * @return void   
     * @access public 
     */
    public function setMessage( $message ) {
        $this->message = $message;
    }

    /*
	* Method to set the timeout.
	* @param integer unit in miliseconds.
	*/

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $miliSec Parameter description (if any) ...
     * @return void   
     * @access public 
     */
    public function setTimeout( $miliSec ) {
        $this->timeout = $miliSec;
    }

    /*
	* Method to set the hide type to Hidden.
	*/

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
    public function setHideTypeToHidden( ) {
        $this->typeHide = 'hidden';
    }

    /*
	* Method to set the hide type to Hidden.
	*/

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
    public function setHideTypeToNone( ) {
        $this->typeHide = 'none';
    }

    /*
	* Private method to insert the java script function.
	*/

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
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
	*/

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
	public function showJScript()
	{
        $this->appendArrayVar('headerParams', $this->_jscript() );
    }
    /*
	* Method to show the message.
	*/

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
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