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

// Include the HTML base class

/**
 * Description for require_once
 */
require_once("abhtmlbase_class_inc.php");
// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
* HTML control class to create anchor (<A href=>) tags
*
* @author Derek Keats
*         
*/
class link extends abhtmlbase implements ifhtml
{
    /**
    *
    * @var string $linkType The type of link, e.g. http, mailto, etc.
    *             If not provided the default http is used.
    */
    public $linkType;

    /**
    *
    * @var string $href The url to load, without the http://
    *             use lower case "none" if you want to produce <a href="#"
    *             Optional
    */
    public $href;

    /**
    *
    * @var string $target The target frame
    */
    public $target;

    /**
    *
    * @var string $link The text to display in the link.
    *             If it is left out the URL is displayed.
    */
    public $link;

    /**
    *
    * @var string $charset Specifies the character encoding of the target URL
    */
    public $charset;

    /**
    *
    * @var string $hreflang Specifies the base language of the target URL
    *             Optional, rarely used
    */
    public $hreflang;
    /**
    *
    * @var string $rel Specifies the relationship between the current
    *             document and the target URL
    *             Optional, rarely used
    *             Values:
    *             alternate
    *             designates
    *             stylesheet
    *             start
    *             next
    *             prev
    *             contents
    *             index
    *             glossary
    *             copyright
    *             chapter
    *             section
    *             subsection
    *             appendix
    *             help
    *             bookmark
    */

    public $rel;

    /**
    *
    * @var string $rev Specifies the relationship between the target
    *             URL and the current document.
    *             Values:
    * @see $rel
    */
    public $rev;

    /**
    *
    * @var string $type Specifies the MIME (Multipurpose Internet Mail Extensions)
    *             type of the target URL
    */
    public $type;

    /**
    *
    * @var string $anchor specifies the anchor (#) on a page for the link
    */
    public $anchor;

    /**
    *
    * @var string $title specifies the link title
    */
    public $title;

    /**
    *
    * @var string $extra Gives the user more flexability
    */
  //  public $extra;

    /**
    * Initialization method to set default values
    */
    public function link($href=null)
    {
        $this->href=$href;
    }

    /**
    * Show method
    */
    public function show()
    {
        return $this->_buildLink();
    }

    /*-------------- PRIVATE METHODS BELOW LINE ------------------*/

    /**
    * Method to build the link from the parameters
    */
    private function _buildLink()
    {
        $ret = "<a href=\"";
        if ($this->linkType!="none" && $this->linkType!=Null) {
            if ($this->linkType=='mailto') {
                $ret .= $this->linkType.":";
            }else{
                $ret .= $this->linkType."://";
            }
        }
        if ($this->href) {
            $ret .= $this->href;
            if ($this->anchor) {
                $ret.='#'.$this->anchor;
            }
            $ret .= "\" ";
        } else {
            die ("Missing URL"); //MULTILINGUALIZE
        }
        if ($this->name) {
            $ret .= " name=\"" . $this->name . "\" ";
        }
        if ($this->cssId) {
            $ret .= " id=\"" . $this->cssId . "\" ";
        }
        if ($this->cssClass) {
            $ret .= " class=\"" . $this->cssClass . "\" ";
        }
        if ($this->title) {
            $ret .= " title=\"" . $this->title . "\" ";
        }
        if ($this->style) {
            $ret .= " style=\"" . $this->style . "\" ";
        }
        if ($this->dir) {
            $ret .= " dir=\"" . $this->dir . "\" ";
        }
        if ($this->tabindex) {
            $ret .= " tabindex=\"" . $this->tabindex . "\" ";
        }
        if ($this->accesskey) {
            $ret .= " accesskey=\"" . $this->accesskey . "\" ";
        }
        if ($this->target) {
            $ret .= " target=\"" . $this->target . "\" ";
        }
        if ($this->charset) {
            $ret .= " charset=\"" . $this->charset . "\" ";
        }
        if ($this->hreflang) {
            $ret .= " hreflang=\"" . $this->hreflang . "\" ";
        }
        if ($this->rel) {
            $ret .= " rel=\"" . $this->rel . "\" ";
        }
        if ($this->rev) {
            $ret .= " rev=\"" . $this->rev . "\" ";
        }
        if ($this->type) {
            $ret .= " type=\"" . $this->type . "\" ";
        }
        if ($this->extra) {
            $ret .= ' '.$this->extra;
        }
        $ret .= ">".$this->link."</a>";
        return $ret;
    }
}

?>