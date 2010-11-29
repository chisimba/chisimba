<?php
/**
 *
 * A button class using DOM extension
 *
 * This file contains the button class which is used to generate
 * HTML button elements for forms
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
 * HTML button elements for forms
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
 *            $this->objButton=new button('buttonname');
 *            $this->objButton->setValue('Button Value');
 *            $this->objButton->setOnClick('alert(\'An onclick Event\')');
 *            $this->objButton->setToSubmit();  //If you want to make the button a submit button
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

    public $name;
    public $value;
    public $onclick;
    public $extra;
    public $cssId;
    public $cssClass;
    public $objDom;


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
    
    public function show($caption=null, $name=null, $value = null, $onclick = null) {
        if($this->sexyButtons == TRUE) {
            $button = $this->objDom->createElement('button');
            $button->setAttribute('class','sexybutton ');
            $button->setAttribute('value',$caption);
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
        if ($this->name) {
            $button->setAttribute('name',$this->name);
        }
        if ($this->cssId) {
            $button->setAttribute('id',$this->cssId);
        }
        if ($this->cssClass) {
            $button->setAttribute('class',$this->cssClass);
        }
        if ($this->onclick) {
            $button->setAttribute('onclick',$this->onclick);
        }
        if ($this->extra) {
            $button->setAttribute('extra',$this->extra);
        }
        $button = $this->objDom->appendChild($button);
        $ret = $this->objDom->saveHTML();
        //die($ret);
        return $ret;
    }

    public function setValue($param, $value) {
        $this->$param = $value;
    }

    /**
     * Method to set the action for the onclick event
     * for the button
     *
     * @param string $onclick
     * @return void
     * @access public
     */
    public function setOnClick($onclick)
    {
        $this->onclick = $onclick;
    }
    
    /**
     * Method to set the iconclass for the sexy buttons
     * Can be one of: ok, cancel, add, delete, download, download2, upload, search, find, first, prev, next, last, play, pause, 
     *                rewind, forward, stop, reload, sync, save, email, print, heart, like, dislike, accept, decline, home, 
     *                help, info, cut, copy, paste, erase, undo, redo, edit, calendar, user, settings, wrench, cart, wand
     *
     * @param string $onclick
     * @return void
     * @access public
     */
    public function setIconClass($iconclass)
    {
        $this->iconclass = $iconclass;
    }

    /**
     * Method to set the cssClass private variable
     * which determines the DOM class of the button as
     * definied in the CSS
     *
     * @param string $cssClass the class
     * @return void
     * @access public
     */
    function setCSS($cssClass)
    {
        $this->cssClass = $cssClass;
    }

    /**
     * Method to set the cssId private member
     * which determines the DOM id of the button
     *
     * @param string $cssId the Id
     * @return void
     * @access public
     */
    public function setId($cssId)
    {
        $this->cssId = $cssId;
    }

    /**
     * Method used to set the button as
     * a submit button for a form
     *
     * @return void
     * @access public
     */
    public function setToSubmit()
    {
        $this->issubmitbutton = true;
    }

    /**
     * Sets the button type to reset.
     *
     * @access public
     */
    public function setToReset()
    {
        $this->isresetbutton = true;
    }
}

?>