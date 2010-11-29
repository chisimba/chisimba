<?php
/**
 *
 * A button class using DOM extension
 *
 * This file contains the button class which is used to generate
 * HTML button elements for forms. It was modified after the original
 * HTMLelements button class by Derek Keats as part of the Chisimba
 * hackathon 2010 11 29. Unlike HTMLelements, this class extends object
 * and must be instantiated using $this->newObject('htmlbutton', 'htmldom')
 *
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

/**
 * A button class using DOM extension
 *
 * This file contains the button class which is used to generate
 * HTML button elements for forms. It was modified after the original
 * HTMLelements button class by Derek Keats as part of the Chisimba
 * hackathon 2010 11 29. Unlike HTMLelements, this class extends object
 * and must be instantiated using $this->newObject('htmlbutton', 'htmldom')
 *
 * @category  Chisimba
 * @package   htmldom
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @author    Derek Keats <derek@dkeats.com>
 *
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: button_class_inc.php 16438 2010-01-22 15:38:42Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @example:
 *       $objButt = $this->newObject('htmlbutton', 'htmldom');
 *       $objButt->setValue("cssId","what_a_long_id_hey");
 *       $objButt->setValue('onclick','javascript:alert("Some alert");');
 *       $str = $objButt->show("TEST SUCCESSFUL YAY YAY YIPPEE");
 */
class htmlbutton extends object
{

    /**
    *
    * The javascript to be executed on submit, if any.
    *
    * @access public
    * @var string $onsubmit
    *
    */
    public $onsubmit;

    /**
    * Whether the button is a submit button or not.
    * @var boolean $issubmitbutton
    * @access public
    *
    */
    public $issubmitbutton;

    /**
     * If true, the button type should be set to "reset".
     *
     * @access public
     * @var boolean $isresetbutton
     */
    public $isresetbutton;
    
    /**
    *  Whether or not to use the sexybuttons interface elements.
     * @var string $iconclass
     * @access public
     * 
    */
    public $sexyButtons = TRUE;

    /**
     * Holds the name of the button, and is set using
     * $this->setVar()
     *
     * @var string $name
     * @access private
     *
     */
    private $name;

    /**
     * Holds the value of the button, and is set using
     * $this->setVar()
     *
     * @var string $name
     * @access private
     *
     */
    private $value;

    /**
     * Holds the unClick javascript event for the button, and is set using
     * $this->setVar()
     *
     * @var string $name
     * @access private
     *
     */
    private $onclick;
    
    /**
     * Holds the CSS id for the button, and is set using
     * $this->setVar()
     * 
     * @var string $name
     * @access private
     * 
     */
    private $cssId;

    /**
     * Holds the CSS class for the button, and is set using
     * $this->setVar(). Not used if sexybuttons is true.
     *
     * @var string $name
     * @access private
     *
     */
    private $cssClass;

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
    * Intialiser for the htmldom BUTTON object
    *
    * @access public
    * @return void
    *
    */
    public function init()
    {
        // Instantiate the built in PHP DOM extension and create DOM document.
        $this->objDom = new DOMDocument();
    }

    /**
     *
     * Standard show function to render the button using the DOM document
     * object
     *
     *
     * @param <type> $caption
     * @param <type> $name
     * @param <type> $value
     * @param <type> $onclick
     * @return <type>
     */
    public function show($caption=null) {
        if($this->sexyButtons == TRUE) {
            $button = $this->objDom->createElement('button');
            $button->setAttribute('class','sexybutton ');
            $button->setAttribute('value',$caption);
            // The sexybutton needs to be inside two span tags
            $span1 = $this->objDom->createElement('span');
            $button->appendChild($span1);
            $span2 = $this->objDom->createElement('span');
            $span1->appendChild($span2);
            $text = $this->objDom->createTextNode($caption);
            $span2->appendChild($text );
        } else {
            $button = $this->objDom->createElement('input');
            $button->setAttribute('value',$caption);
            if ($this->cssClass) {
                $button->setAttribute('class',$this->cssClass);
            }
        }
        // See if it is submit or reset.
        if ($this->issubmitbutton) {
            $button->setAttribute('type','submit');
        } elseif ($this->isresetbutton) {
            $button->setAttribute('type','reset');
        } else {
            $button->setAttribute('type','submit');
        }
        // If there is a name, then use it.
        if ($this->name) {
            $button->setAttribute('name',$this->name);
        }
        // If a css id is set, then add it as an attribute
        if ($this->cssId) {
            $button->setAttribute('id',$this->cssId);
        }
        // If there is an onclick event specified, add it as an attribute.
        if ($this->onclick) {
            $button->setAttribute('onclick',$this->onclick);
        }
        $button = $this->objDom->appendChild($button);
        $ret = $this->objDom->saveHTML();
        //die($ret);
        return $ret;
    }

    /**
     *
     * A standard setter. The following params may be set here
     * $onclick - A javascript that is executed on clicking the button
     * $iconclass - The icon class to use (only relevant if using sexybuttons)
     *      Can be one of: ok, cancel, add, delete, download, download2, upload,
     *      search, find, first, prev, next, last, play, pause, rewind, forward,
     *      stop, reload, sync, save, email, print, heart, like, dislike,
     *      accept, decline, home, help, info, cut, copy, paste, erase, undo,
     *      redo, edit, calendar, user, settings, wrench, cart, wand
     * $cssClass - A CSS class to use in the button
     * $cssId - A CSS id to use in the button
     * $isresetbutton  - Set it to a reset button
     * $issubmitbutton - Set it to a submit button
     *
     * @param string $param The name of the parameter to set
     * @param string $value The value to set the parameter to
     * @access public
     */
    public function setValue($param, $value) {
        $this->$param = $value;
    }
    
}
?>