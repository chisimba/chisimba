<?php

/**
  /* Simple class for outputting '<a href' links>
 *
 * PHP version 5
 *
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Nguni Phakela
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: href_class_inc.php 11055 2008-10-25 16:25:24Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @example    $str = $this->newObject("htmlhref", "htmldom");
 *            $str->setValue("link", "#");
 *            $str->setValue("text", "Click Here");
 *            $str->setValue("other", "id=testclick");
 *            $str = $str->show();
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

class htmlhref extends object {

    /**
      * Holds the name of the htmlhref, and is set using $this->setValue()
      *
      * @var string $name
      * @access private
      *
      */
    private $name;
    /**
     * Holds the href link
     * @var    string
     * @access private
     */
    private $link;

    /**
     * Holds the text for the link
     * @var    string
     * @access private
     */
    private $text;

    /**
     * Holds other information in a string
     * @var    string
     * @access private
     */
    private $other;

    /**
     *
     * Object to hold the dom document
     *
     * @var string Object $objDom
     * @access private
     */
    private $objDom;

    /**
     *
     * Intialiser for the htmldom <A> object
     *
     * @access public
     * @return void
     *
     */
    public function init() {
        //Instantiate the built in PHP DOM extension and create DOM document.
        $this->objDom = new DOMDocument();
    }

    /**
     * A method to show the href link
     * 
     * @return string Return href link
     * @access public
     */
    public function show() {
        $href = $this->objDom->createElement('a');

        if($this->name) {
            $href->setAttribute('name', $this->name);
        }
        if($this->link) {
            $href->setAttribute('href', $this->link);
        }
        if($this->text) {
            $caption = $this->text;
            $text = $this->objDom->createTextNode($caption);
            $href->appendChild($text);
        }
        if($this->other) {
            //split up the string
            list($attr, $value)= explode("=",$this->other);
            $href->setAttribute($attr, $value);
        }

        $this->objDom->appendChild($href);
        $ret = $this->objDom->saveHTML();
        
        return $ret;
    }

    /**
     *
     * A standard setter. The following params may be set here
     * $link - Specifies the destination of a link
     * $text- The text that is displayed for the link
     * $other - Any other attribute that is unknown that the user wants to set
     *
     * @param string $param The name of the parameter to set
     * @param string $value The value to set the parameter to
     * @access public
     */
    public function setValue($param, $value) {
        $this->$param = $value;
    }

    /**
      * A standard getter. The following params may be retrieved here
      * $link - Specifies the destination of a link
      * $text- The text that is displayed for the link
      * $other - Any other attribute that is unknown that the user wants to set
      *
      * @param string $param The name of the parameter to set
      * @access public
      */
    public function getValue($param) {
        return $this->$param;
    }

}
// end of class
?>