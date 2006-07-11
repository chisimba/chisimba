<?php

/**
* This class generates a DomTT tool Tip that can be 
* used for any link in your code
*
* @package cssLayout
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version 1
* @author Wesley Nitsckie
* @example
* $objDomTT->show('Some Title', 'This is a shortish message that you can add', 'The link lable or title of image', 'http://somewhere.com');
*/

class domtt
{
    /**
     * Constructor
     */
    public function init()
    {
        $this->javaScriptIsSet = False;
        
    }
    
    
    /**
     * Method to show the domTT Item
     * 
     * @return string
     * @access public
     * @author Wesley Nitsckie
     */
    public function show( $title = NULL, $message = NULL, $linkText = NULL, $url = NULL)
    {
        $this->url = $url;
        
        $this->linkText = $linkText;
        
        $this->message = $message;
        
        $this->title = $title;
        
        
        $str = "<a href=\"".$this->url."\" 
            `       onmouseover=\"this.style.color = '#D17E62'; 
                        domTT_activate(this, event, 'content', 
                        '".$this->title."'<p>
                        ".$this->messsage."</p>',
                        'trail', true, 'fade', 'both', 'fadeMax', 87, 'styleClass', 'niceTitle');\"
                         onmouseout=\"this.style.color = ''; domTT_mouseout(this, event);\">
                         ".$this->linkText."</a>";
        
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
        
       $str = '<script type="text/javascript" language="javascript" src="domtt/domLib.js"></script>
        <script type="text/javascript" language="javascript" src="domtt/fadomatic.js"></script>
        <script type="text/javascript" language="javascript" src="domtt/domTT.js"></script>
        <script>
            var domTT_styleClass = \'domTTOverlib\';
            var domTT_oneOnly = true;
        </script>';
        
        $this->javaScriptIsSet = True;
        
        return $str;
        
    }
}
?>