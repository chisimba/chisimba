<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// Include the HTML interface class
require_once("ifhtml_class_inc.php");

/**
 * HTML control class to create and IFRAME(<IFRAME>) tag
*/
class iframe implements ifhtml
{
	/**
	* Define all vars, these are obvious so not individually labelled
	*/
    public $width;
    public $height;
    public $src;
    public $align;
    public $frameborder; //must be 0 or 1
    public $marginheight;
    public $marginwidth;
    public $name;
	public $id;
    public $scrolling;
    public $theFrame;
    

    /**
     * Initialization method to set default values
     */
    public function iframe()
    {
        $this->width="800";
        $this->height="600";
    }
    
    /**
    * Method to return an invisible IFRAME
    */
    public function invisibleIFrame($src)
    {
        $this->width=0;
        $this->height=0;
        $this->src="http://".$src;
        return $this->_buildIframe();
    }
    
    /**
    * Show method
    */
    public function show()
    {
        return $this->_buildIframe();
    }
    

    /*-------------- PRIVATE METHODS BELOW LINE ------------------*/

    /** 
    * Method to build the Iframe from the parameters
    */
    private function _buildIframe()
    {
        $ret="<iframe width=\"".$this->width."\" height=\"".$this->height."\" ";
        $ret .= "src=\"".$this->src."\"";
        if ($this->align) {
            $ret .= " align=\"".$this->align."\" ";
        }
        if (isset($this->frameborder)) {
            $ret .= " frameborder=\"".$this->frameborder."\" ";
        }
        if ($this->align) {
            $ret .= " align=\"".$this->align."\" ";
        }
        if ($this->marginheight) {
            $ret .= " marginheight=\"".$this->marginheight."\" ";
        }
        if ($this->marginwidth) {
            $ret .= " marginwidth=\"".$this->marginwidth."\" ";
        }
        if ($this->name) {
            $ret .= " name=\"".$this->name."\" ";
        }
        if ($this->id) {
            $ret .= " id=\"".$this->id."\" ";
        }
        if ($this->scrolling) {
            $ret .= " scrolling=\"".$this->scrolling."\" ";
        }
        $ret .= ">Iframe support required</iframe>";
        return $ret;
    }
}
?>