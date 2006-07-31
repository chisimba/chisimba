<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// Include the HTML interface class
require_once("ifhtml_class_inc.php");

/**
* HTML control class to create image tags
*
* @author Derek Keats
*
* @todo -c Implement .mouseover effects --> can someone take this over?
*
*/
class image implements ifhtml
{
    /**
    * Define all vars
    */
    public $width;
    public $height;
    public $src;
    public $align;
    public $alt;
    public $imageTag;
    public $border;
    // Javascript tags
    public $mouseover;
    public $mouseout;
    public $onclick;
    public $over_image_src;

    /**
    * Initialization method to set default values
    */
    public function image()
    {
        $this->alt = null;
        $this->imageTag = null;
        $this->border = 0;
    }

    /**
    * Method to return an invisible IFRAME
    */
    public function show()
    {
        $this->_buildImage();
        return $this->imageTag;
    }

    /*-------------- PRIVATE METHODS BELOW LINE ------------------*/

    /**
    * Method to build the Iframe from the parameters
    */
    private function _buildImage() {
        $this->imageTag = "<img src=\"" . $this->src . "\"";
        if ($this->width && $this->height) {
            $this->imageTag .= " width=\"" . $this->width . "\"";
            $this->imageTag .= " height=\"" . $this->height . "\"";
        } /*else {
            $this->imageTag .= " ".$this->_getImageSize();
        } */
        if ($this->height) {

        }
        if ($this->align) {
            $this->imageTag .= " align=\"" . $this->align . "\"";
        }
        if ($this->alt) {
            $this->imageTag .= " alt=\"" . $this->alt . "\"";
        }
        if ($this->border) {
            $this->imageTag .= " border=\"" . $this->border . "\"";
        }
        $this->imageTag .= " />";

    }

    /**
    * Get the size of an image
    * Function produces a warning error if the path to the src is an http://... path.
    * This bug has been fixed in PHP5.
    * @return string the width and height tag for the image
    */
    private function _getImageSize()
    {
        $image_size = getimagesize ($this->src);
        return $image_size['3']; //the formatted witdth and height tag
    }
}
?>
