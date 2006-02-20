<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
* windowPop class to use to make popup windows.
* 
* @package windowPop
* @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version $Id$;
* @author Derek Keats 
* @example: 
*   //Popup window
*   $this->objPop=&new windowpop;
*   $this->objPop->set('location','/modules/htmltabledemo/popup.htm');
*   $this->objPop->set('linktext','Click me baby');
*   $this->objPop->set('width','200'); 
*   $this->objPop->set('height','200');
*   $this->objPop->set('left','300');
*   $this->objPop->set('top','400');
*   //leave the rest at default values
*   $this->objPop->putJs(); // you only need to do this once per page
*   echo $this->objPop->show();
*/
class windowPop {
    /**
    * 
    * @var string $location: The page to appear in the window
    */
    var $location;
    /**
    * 
    * @var string $window_name: The name for the popup window
    */
    var $window_name;
    /**
    * 
    * @var string $features: The third parameter that holds name-value pairs below
    */
    var $features;
    /**
    * 
    * @var boolean $directories: Controls the standard browser directory buttons
    */
    var $directories;
    /**
    * 
    * @var int $width: Specifies the width of the window in pixels
    */
    var $width;
    /**
    * 
    * @var int $height: Specifies the height of the window in pixels
    */
    var $height;
    /**
    * 
    * @var int $top: Specifies the distance from the top of the window in px
    */
    var $top;
    /**
    * 
    * @var int $left: Specifies the distance from the left of the window in px
    */
    var $left;
    /**
    * 
    * @var boolean $menubar: Controls the menu at the top of the window, defaults to no
    */
    var $menubar;
    /**
    * 
    * @var boolean $resizable: Controls the ability to resize the window, defaults to no
    */
    var $resizable;
    /**
    * 
    * @var boolean scrollbars: Controls the horizontal and vertical scrollbars
    */
    var $scrollbars;
    /**
    * 
    * @var boolean $status: Controls the status bar at the bottom of the window
    */
    var $status;
    /**
    * 
    * @var boolean $toolbar: Controls the standard browser toolbar
    */
    var $toolbar;
    /**
    * 
    * @var string $linktext: The text (or image tag) for the link
    */
    var $linktext;
    /**
    * 
    * @var string $js: The javascript for the page or page header
    */
    var $js;
    /**
    * @var bool $js_iswritten: True if the javascript has already been written 
    * to the page, else false TRUE | FALSE
    */
    var $js_iswritten;
    /**
    * Method to establish the default values
    */
    function windowPop()
    {
        $this->window_name = "new";
        $this->directories = "";
        $this->width = "640";
        $this->height = "480";
        $this->menubar = "no";
        $this->resizable = "no";
        $this->scrollbars = "no";
        $this->status = "no";
        $this->toolbar = "no";
        $this->js = "<script language=\"javascript\">\n"
         . "function openWindow(theURL,winName,features) { \n"
         . "  window.open(theURL,winName,features);\n } \n</script>";
    } 

    /**
    * function to set the value of one of the properties of this class
    * 
    * @var string $property: The name of the property to set
    * @var mixed $value: The value to set the property to be
    */
    function set($property, $value)
    {
        $this->$property = $value;
    }
    
    /**
    * Method to put the javascript in the page and lock it so it can't
    * be written again
    */
    function putJs()
    {
        if (!$this->js_iswritten) {
            $this->js_iswritten=TRUE;
            return $this->js;
        } else {
            return $this->js;
        }
    }

    /**
    * Method to show the window link
    * @return string The formatted link
    */
    function show()
    {
        
        $this->features="toolbar=".$this->toolbar.", "
        ."menubar=".$this->menubar.", "
        ."width=".$this->width.", "
        ."height=".$this->height.", "
        ."resizable=".$this->resizable.", "
        ."scrollbars=".$this->scrollbars.", "
        ."toolbar=".$this->toolbar;
        //check if there are left and top elements
        if ($this->top) {
            $this->features .= " top=".$this->top." screenY=".$this->top;
        }
        if ($this->left) {
            $this->features .= " left=".$this->left." screenY=".$this->left;
        }
        return $this->putJs()."<a href=\"javascript:;\" onClick=\"openWindow('"
         . $this->location . "','" . $this->window_name . "','" . $this->features . "')\">"
         . $this->linktext . "</a>";
    } 
} 

?>