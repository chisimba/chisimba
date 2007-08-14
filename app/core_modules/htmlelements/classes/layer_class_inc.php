<?php

/**
 * Layer class
 * 
 * HTML control class to create layers (<DIV>) tags
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
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
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
// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
 * HTML control class to create layers (<DIV>) tags
*/
class layer extends object implements ifhtml
{
    /**
    *
    * @var string $id: the ID tag from the CSS
    */
    public $id;
    /**
    * @var string $name: Optional, the name to apply to the object
    */
    public $name;
    /**
    * @var        string $css_class: The name of the CSS class to apply
    *                    to the layer
    * @deprecated use cssClass
    */
    public $css_class;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $cssClass;
    /**
    * @var int $width: The width of the layer
    */
    public $width;
    /**
    * @var int $height: The height of the layer
    */
    public $height;
    /**
    * @var int $zIndex: The z-index (layer order) of the layer
    */
    public $zIndex;
    /**
    * @var  string $position: The position of the layer (absolute|
    * @todo -clayer Implement layer: not yet properly implemented
    */
    public $position;
    /**
    * @var  string $floating: The position of the layer (left|right|top|bottom)
    * @todo -clayer Implement layer: not yet properly implemented
    */
    public $floating;
    /**
    * @var  int $left: The position of the layer from the left margin
    *           if position:absolute is used
    * @todo -clayer Implement layer: not yet properly implemented
    */
    public $left;
    /**
    * @var  int $top: The position of the layer from the top margin
    *           if position:absolute is used
    * @todo -clayer Implement layer: not yet properly implemented
    */
	public $top;
    /**
    * @var string $background_color: The background colour of the layer
    */
    public $background_color;
    /**
    * @var string $visibility: layer visibility (DEFAULT | INHERIT | VISIBLE | HIDDEN)
    */
    public $visibiity;
    /**
    * @var string $background_image: URL for an image to apply to the background
    */
    public $background_image;
    /**
    * @var string $border: border width for the layer
    */
    public $border;
    /**
    * @var string $padding: padding for the layer
    */
    public $padding;
    /**
    * @var string $overflow: what to do when text overflows the layer
    *             (VISIBLE | HIDDEN | SCROLL | AUTO)
    */
    public $overflow;
    /**
    * @var  string $use_style: An additional style tag to use
    * @todo -clayer Implement layer THis is not yet implemented
    */
    public $use_style;
    /**
    * @var string $str: the final output string
    */
	public $str;
    /**
    * @var string $align: use to generate quick aligned layers (e.g. center, left, right
    */
    public $align;
    /**
    * @var string $textalign: use to generate text aligned within the layer
    *             center|left|right
    */
    public $textalign;

    /**
    * @var string $display: use to determine if the layer takes up space
    *             block|none
    */
    public $display;

    /**
    * @var string $cursor: The type of cursor to display
    *             auto|crosshair|default|pointer|move|text|wait|help
    */
    public $cursor;

    /**
    * @var string $onclick: The actio to perform on the onclick event
    */
    public $onclick;

    /**
     * Initialization method to set default values
     * @todo -clayer Implement layer. Which ones should be  Null?
     */
    public function init()
    {
        $this->id = null;
        $this->name = null;
        $this->cssClass = null;
        $this->onclick = null;
        $this->width = null;
        $this->height = null;
        $this->position = null;
        $this->zIndex = null;;
        $this->position = null;
        $this->left = null;
		$this->top = null;
        $this->background_color = null;
        $this->visibility = null;
        $this->background_image = null;
        $this->border = null;
        $this->padding = null;
        $this->overflow = null;
        $this->align = null;
        $this->textalign = null;
        $this->clear = null;
        $this->cursor = 'default';
        $this->display = 'block';
    }

    /**
    * Constructor method to call the layer class correctly
    *
    * @var string $strn The string to pass to the layer
    */
    public function layer()
    {
        $this->str='';
        return $this->init();
    }

    /**
     * Method to add a string to a layer and return the resulting
     * string
     *
     * @deprecated Rather use the show method to display the layer
     *             This functionality will be moved to show shortly
     *             
     */
    public function addToLayer()
    {
        return $this->show();
    }

    /**
    * Show method for compatibility with other classes in htmlElements
    */
    public function show()
    {

        $ret = "<div";
        if ($this->id) {
            $ret .= " id=\"" . $this->id . "\"";
        }
        if ($this->css_class) {
            $ret .= " class=\"" . $this->css_class . "\" ";
        }
        if ($this->cssClass) {
            $ret .= " class=\"" . $this->cssClass . "\" ";
        }
		if ($this->checkForStyle()) {
			$ret .= " style=\"";
			if ($this->position) {
			    $ret .= "position: ".$this->position."; ";
			}
			if ($this->floating) {
			    $ret .= "float: ".$this->floating."; ";
			}
		    if ($this->textalign) {
	            $ret .= "align: " . $this->align . "; ";
	        }
	        if ($this->width) {
	            $ret .= "width: " . $this->width . "; ";
	        }
	        if ($this->height) {
	            $ret .= "height: " . $this->height . "; ";
	        }
	        if ($this->left) {
	            $ret .= "left: " . $this->left . "; ";
	        }
	        if ($this->top) {
	            $ret .= "top: " . $this->top . "; ";
	        }
	        if ($this->background_color) {
	            $ret .= " background-color: " . $this->background_color . "; ";
	        }
	        if ($this->background_image) {
	            $ret .= "background_image: " . $this->background_image . "; ";
	        }
	        if ($this->border) {
	            $ret .= "border: " . $this->border . "; ";
	        }
	        if ($this->padding) {
	            $ret .= "padding: " . $this->padding . "; ";
	        }
	        if ($this->overflow) {
	            $ret .= "overflow: " . $this->overflow . "; ";
	        }
            if ($this->visibility) {
	            $ret .= "visibility : " . $this->visibility . "; ";
	        }
            if ($this->display) {
	            $ret .= "display : " . $this->display . "; ";
	        }
            if ($this->zIndex) {
	            $ret .= "zIndex : " . $this->zIndex . "; ";
	        }
            if ($this->cursor) {
	            $ret .= "cursor : " . $this->cursor . "; ";
	        }
			$ret.="\"";
		}
        if ($this->align) {
            $ret .= " align=\"" . $this->align . "\"";
        }
        if ($this->onclick) {
            $ret .= " onclick=\"" . $this->onclick . "\"";
        }
        $ret .= ">" . $this->str . "</div>\n\n";
        return $ret;
    }

	/**
	* Method to look at all the properties and see if we need
	* to build a STYLE= element
	*/
    public function checkForStyle()
    {
        $classVars = get_class_vars(get_class($this));
        foreach ($classVars as $name => $value) {
            if ($name != 'id' && $name != 'name'
              && $name != 'css_class'
              && $name != 'cssClass'
              && $name != 'onclick') {
                if ($this->$name) {
                    return true;
                }
            }
        }
        return False;
    }

	/**
	* Method to add to the end of the output string
    * @var string $strn: The string to add to the end
	*/
	public function addToStr($strn)
	{
		$this->str .= $strn;
	}


	/**
	* Method to add to the top of the output string
    * @var string $strn: The string to add to the top
	*/
	public function addToStrTop($strn)
	{
		$this->str = $strn.$this->str;
	}
}

?>
