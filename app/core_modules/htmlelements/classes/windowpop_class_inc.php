<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// Include the HTML interface class
require_once("ifhtml_class_inc.php");

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
class windowPop implements ifhtml
{
    /**
    *
    * @var string $location: The page to appear in the window
    */
    public $location;
    /**
    *
    * @var string $window_name: The name for the popup window
    */
    public $window_name;
    /**
    *
    * @var string $features: The third parameter that holds name-value pairs below
    */
    public $features;
    /**
    *
    * @var boolean $directories: Controls the standard browser directory buttons
    */
    public $directories;
    /**
    *
    * @var int $width: Specifies the width of the window in pixels
    */
    public $width;
    /**
    *
    * @var int $height: Specifies the height of the window in pixels
    */
    public $height;
    /**
    *
    * @var int $top: Specifies the distance from the top of the window in px
    */
    public $top;
    /**
    *
    * @var int $left: Specifies the distance from the left of the window in px
    */
    public $left;
    /**
    *
    * @var boolean $menubar: Controls the menu at the top of the window, defaults to no
    */
    public $menubar;
    /**
    *
    * @var boolean $resizable: Controls the ability to resize the window, defaults to no
    */
    public $resizable;
    /**
    *
    * @var boolean scrollbars: Controls the horizontal and vertical scrollbars
    */
    public $scrollbars;
    /**
    *
    * @var boolean $status: Controls the status bar at the bottom of the window
    */
    public $status;
    /**
    *
    * @var boolean $toolbar: Controls the standard browser toolbar
    */
    public $toolbar;
    /**
    *
    * @var string $linktext: The text (or image tag) for the link
    */
    public $linktext;
    /**
    *
    * @var string $js: The javascript for the page or page header
    */
    public $js;
    /**
    * @var bool $js_iswritten: True if the javascript has already been written
    * to the page, else false TRUE | FALSE
    */
    public $js_iswritten;
    /**
    * @var string Type of link to use. Can either be 'link' or 'button'
    */
    public $linkType = 'link';
    
    /**
    * Method to establish the default values
    */
    public function windowPop()
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
        $this->js = "<script language=\"javascript\" type=\"text/javascript\">\n"
         . "function openWindow(theURL,winName,features) { \n"
         . "  newwindow=window.open(theURL,winName,features);\n 
              if (window.focus) {newwindow.focus()}\n
	//return false;\n 
         } \n</script>";
    }

    /**
    * function to set the value of one of the properties of this class
    *
    * @var string $property: The name of the property to set
    * @var mixed $value: The value to set the property to be
    */
    public function set($property, $value)
    {
        $this->$property = $value;
    }

    /**
    * Method to put the javascript in the page and lock it so it can't
    * be written again
    */
    public function putJs()
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
    public function show()
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
        
        // Either show as button or link (default)
        if ($this->linkType=='button') {
            
            if (preg_match('/<.+?>/', $this->linktext)) {
                return $this->putJs()."<button onclick=\"javascript:openWindow('"
         . $this->location . "','" . $this->window_name . "','" . $this->features . "')\">"
         . $this->linktext . "</button>";
            } else {
                return $this->putJs()."<input type=\"button\" class=\"button\"  onclick=\"javascript:openWindow('"
         . $this->location . "','" . $this->window_name . "','" . $this->features . "')\" value=\""
         . $this->linktext . "\" />";
            }

            
        } else {
            return $this->putJs()."<a href=\"javascript:openWindow('"
         . $this->location . "','" . $this->window_name . "','" . $this->features . "')\">"
         . $this->linktext . "</a>";
         }
    }
}

?>