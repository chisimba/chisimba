<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
*
* Used to create messages with a timer.
*
* @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @author Jonathan Abrahams
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
        $this->htmlTag = 'SPAN';
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
	*/
    public function setTimeout( $miliSec ) {
        $this->timeout = $miliSec;
    }
    
    /*
	* Method to set the hide type to Hidden.
	*/
    public function setHideTypeToHidden( ) {
        $this->typeHide = 'hidden';
    }
    
    /*
	* Method to set the hide type to Hidden.
	*/
    public function setHideTypeToNone( ) {
        $this->typeHide = 'none';
    }
    
    /*
	* Private method to insert the java script function.
	*/
    public function _jscript() {
        static $count = 0;
        $jscript = "<script>";
        
        if ($this->typeHide == 'hidden') {
            $code = '.visibility = \'hidden\'';
        } else {
            $code = '.display = \'none\'';
        }
        
        $jscript.= "function hidemydiv$count() {
          document.getElementById('$this->cssId').style$code;
        }";
        $jscript.= " setTimeout('hidemydiv$count()', $this->timeout );";
        $jscript.="</script>";
        $count++;
        return $jscript;
    }

    /*
	* Method to show jscript in header.
	*/
	public function showJScript()
	{
        $this->appendArrayVar('headerParams', $this->_jscript() );
    }
    /*
	* Method to show the message.
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
