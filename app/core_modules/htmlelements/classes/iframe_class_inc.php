<?php
/**
 * HTML control class to create layers (<DIV>) tags
*/
class iframe {
	/**
	* Define all vars, these are obvious so no individually labelled
	*/
    var $width;
    var $height;
    var $src;
    var $align;
    var $frameborder; //must be 0 or 1
    var $marginheight;
    var $marginwidth;
    var $name;
	var $id;
    var $scrolling;
    var $theFrame;
    

    /**
     * Initialization method to set default values
     */
    function iframe()
    {
        $this->width="800";
        $this->height="600";
    }
    
    /**
    * Method to return an invisible IFRAME
    */
    function invisibleIFrame($src)
    {
        $this->width=0;
        $this->height=0;
        $this->src="http://".$src;
        return $this->_buildIframe();
    }
    
    /**
    * Show method
    */
    function show()
    {
        return $this->_buildIframe();
    }
    

    /*-------------- PRIVATE METHODS BELOW LINE ------------------*/

    /** 
    * Method to build the Iframe from the parameters
    */
    function _buildIframe()
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