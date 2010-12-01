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
 * example    $str = $this->newObject("htmlhref", "htmldom");
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

class htmlheading extends object {

    /**
      * Holds the name of the button, and is set using $this->setValue()
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
    private $type;

    /**
      * Holds the CSS id for the textarea, and is set using $this->setValue().
      * Value is returned using $this->getValue();
      *
      * @var string $name
      * @access private
      *
      */
    private $cssId;

    /**
      *
      * Holds the CSS class for the button, and is set using $this->setValue().
      * @var string $cssClass:
      * @access private
      */
    private $cssClass;

    /**
     * @var string $align How the header should align on the page
     *
     */
    private $align;
    /**
     *
     * @var string $str The text to place between the header tags
     */
    public $str;

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
     * Method to show the heading
     *
     * @return The heading complete as a string
     */
    public function show() {
        $heading = $this->objDom->createElement($this->type);

        if($this->name) {
            $heading->setAttribute('name', $this->name);
        }
        if($this->cssId) {
            $heading->setAttribute('id', $this->cssId);
        }
        if ($this->cssClass) {
            $heading->setAttribute('class', $this->cssClass);
        }
        if ($this->align) {
            $heading->setAttribute('align', $this->align);
        }
        if($this->str) {
            $text = $this->objDom->createTextNode($this->str);
            $heading->appendChild($text);
        }

        $this->objDom->appendChild($heading);
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