<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
* The DomTT is a tool tip script that displays useful information using a <a> tag
* @package domtt
* @category htmlelement
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version
* @author Wesley  Nitsckie
* @example :
*/

class domtt extends object
{

	
	/**
     * Method to show the domTT Item
     * 
     * @return string
     * @access public
     * @author Wesley Nitsckie
     */
    public function show($title = 'Chisimba', $message = 'replace this message' , $linkText = "replace this link text", $url = "#" , $extra = null )
    {
    	$this->putScripts();
        $this->url = $url;
        
        $this->linkText = $linkText;
        
        $this->message = $message;
        
        $this->title = $title;
        
        $str = "<a  ".$extra."  href=\"".$this->url."\" onmouseover=\"this.style.color = '#D17E62'; domTT_activate(this, event, 'content', '".$this->title."<p>".$this->message."</p>', 'trail', true, 'fade', 'both', 'fadeMax', 87, 'styleClass', 'niceTitle');\" onmouseout=\"this.style.color = ''; domTT_mouseout(this, event);\">".$this->linkText."</a>";
        return $str;
    }
	
	 /**
     * Method to get the javaScript files
     * 
     * @access public
     * @author Wesley Nitsckie
     * @return null
     */
    public function putScripts()
    {
        
       $str = '<script type="text/javascript" language="javascript" src="installer/domtt/domLib.js"></script>
        <script type="text/javascript" language="javascript" src="installer/domtt/fadomatic.js"></script>
        <script type="text/javascript" language="javascript" src="installer/domtt/domTT.js"></script>
        <script>
            var domTT_styleClass = \'domTTOverlib\';
            var domTT_oneOnly = true;
        </script>
        <link rel="stylesheet" href="installer/domtt/example.css" type="text/css" />';
        
        $this->appendArrayVar('headerParams',$str );
        
        return $str;
        
    }
}